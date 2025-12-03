<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Category;
use App\Models\ListingImage;
use App\Models\Offer;
use App\Models\Order;
use App\Models\UserCoupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

// Controlador principal de anuncios: listado, detalle, creación, edición, imágenes, checkout y pagos.
class ListingController extends Controller
{
    // Lista anuncios (excluye vendidos) con filtros y paginación
    public function index(Request $request)
    {
        $query = Listing::query()
            ->where('status', '!=', 'sold')
            ->with(['user:id,name', 'category:id,name', 'images']);

        // Filtros de búsqueda para los anuncios
        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(fn ($qq) => $qq
                ->where('title', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%")
            );
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('city')) {
            $query->where('city', $request->string('city'));
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->string('condition'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $listings = $query->latest()->paginate(20);

        $categories = Category::select('id', 'name')->orderBy('name')->get();

        $favoriteIds = [];
        if ($request->user()) {
            $favoriteIds = $request->user()->favorites()->pluck('favorites.listing_id')->all();
        }

        return $request->wantsJson()
            ? response()->json($listings)
            : view('listings.index', [
                'listings' => $listings,
                'favoriteIds' => $favoriteIds,
                'categories' => $categories,
            ]);
    }

    // Muestra detalle de un anuncio y guarda historial de vistos
    public function show(Request $request, Listing $listing)
    {
        $listing->load(['user:id,name', 'category:id,name', 'images']);
        $isFavorited = false;
        if ($request->user()) {
            $isFavorited = $request->user()->favorites()->whereKey($listing->id)->exists();
        }

        $isBuyer = false;
        if ($request->user()) {
            $isBuyer = Order::where('listing_id', $listing->id)->where('user_id', $request->user()->id)->exists();
        }

        if ($listing->status === 'sold' && (!$request->user() || ($request->user()->id !== $listing->user_id && !$isBuyer))) {
            abort(404);
        }

        // Guardar historial de anuncios recientes en sesión (máx. 10)
        $recent = $request->session()->get('recent_listings', []);
        array_unshift($recent, $listing->id);
        $recent = array_values(array_unique(array_filter($recent, 'is_numeric')));
        $recent = array_slice($recent, 0, 10);
        $request->session()->put('recent_listings', $recent);

        return $request->wantsJson()
            ? response()->json($listing)
            : view('listings.show', compact('listing', 'isFavorited'));
    }

    // Lista los anuncios creados por el usuario autenticado
    public function mine(Request $request)
    {
        $listings = Listing::query()
            ->with(['images', 'category'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return view('listings.mine', [
            'listings' => $listings,
        ]);
    }

    // Pantalla de checkout (elige método de pago y aplicar cupón)
    public function checkout(Request $request, Listing $listing)
    {
        abort_if($request->user()->id === $listing->user_id, 403);

        $listing->loadMissing(['category', 'user']);

        $activeOffer = $this->resolveAcceptedOffer($request, $listing);
        $effectivePrice = $this->effectivePrice($listing, $activeOffer);

        return view('listings.checkout', [
            'listing' => $listing,
            'coupons' => $request->user()->coupons()->whereNull('redeemed_at')->get(),
            'offer' => $activeOffer,
            'effectivePrice' => $effectivePrice,
        ]);
    }

    // Procesa selección de método de pago y cupón, redirige a pantalla de pago
    public function selectPayment(Request $request, Listing $listing)
    {
        abort_if($request->user()->id === $listing->user_id, 403);

        $validated = $request->validate([
            'payment_method' => ['required', 'in:card,paypal'],
            'coupon_id' => ['nullable', 'integer'],
        ]);

        $couponId = $validated['coupon_id'] ?? null;
        $coupon = $this->validateCouponSelection($request, $couponId);
        if (!is_null($couponId) && !$coupon) {
            return back()->withErrors(['coupon_id' => 'Cupón no válido.'])->withInput();
        }

        $routeParams = ['listing' => $listing];
        if ($coupon) {
            $routeParams['coupon'] = $coupon->id;
        }
        $activeOffer = $this->resolveAcceptedOffer($request, $listing);
        if ($activeOffer) {
            $routeParams['offer'] = $activeOffer->id;
        }

        return match ($validated['payment_method']) {
            'card' => redirect()->route('listings.checkout.card', $routeParams),
            'paypal' => redirect()->route('listings.checkout.paypal', $routeParams),
            default => back(),
        };
    }

    // Muestra formulario de pago con tarjeta
    public function checkoutCardForm(Request $request, Listing $listing)
    {
        abort_if($request->user()->id === $listing->user_id, 403);

        $listing->loadMissing(['category', 'user']);

        $activeOffer = $this->resolveAcceptedOffer($request, $listing);
        $effectivePrice = $this->effectivePrice($listing, $activeOffer);

        return view('listings.checkout_card', [
            'listing' => $listing,
            'coupon' => $this->ensureCouponFromQuery($request),
            'offer' => $activeOffer,
            'effectivePrice' => $effectivePrice,
        ]);
    }

    // Procesa pago con tarjeta (aplica cupón, suma puntos, crea pedido y marca vendido)
    public function processCardPayment(Request $request, Listing $listing)
    {
        abort_if($request->user()->id === $listing->user_id, 403);

        $validated = $request->validate([
            'card_holder' => ['required', 'string', 'max:120'],
            'card_number' => ['required', 'regex:/^(\d{4}\s){3}\d{4}$/'],
            'expiry' => ['required', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'],
            'ccv' => ['required', 'digits:3'],
            'coupon_id' => ['nullable', 'integer'],
            'offer_id' => ['nullable', 'integer'],
        ], [
            'card_number.regex' => 'El número de tarjeta debe tener 16 dígitos agrupados en bloques de 4 separados por espacio.',
            'expiry.regex' => 'La fecha debe tener el formato MM/AA.',
        ]);

        $coupon = $this->validateCouponSelection($request, $request->input('coupon_id'));
        if ($request->filled('coupon_id') && !$coupon) {
            return back()->withErrors(['coupon_id' => 'Cupón no válido.'])->withInput();
        }

        $offer = $this->resolveAcceptedOffer($request, $listing, $request->input('offer_id'));

        $basePrice = $this->effectivePrice($listing, $offer);
        $discount = 0;
        if ($coupon) {
            $discount = min($coupon->value, $basePrice);
            $coupon->update(['redeemed_at' => now()]);
        }
        $finalAmount = max(0, $basePrice - $discount);

        $points = (int) floor($finalAmount);
        if ($points > 0) {
            $request->user()->increment('points', $points);
        }

        $this->recordOrder($request->user()->id, $listing, $finalAmount);
        $listing->update(['status' => 'sold']);

        return redirect()->route('listings.index')->with('status', 'Pago con tarjeta registrado (demo).');
    }

    // Muestra formulario de pago con PayPal
    public function checkoutPaypalForm(Request $request, Listing $listing)
    {
        abort_if($request->user()->id === $listing->user_id, 403);

        $listing->loadMissing(['category', 'user']);

        $activeOffer = $this->resolveAcceptedOffer($request, $listing);
        $effectivePrice = $this->effectivePrice($listing, $activeOffer);

        return view('listings.checkout_paypal', [
            'listing' => $listing,
            'coupon' => $this->ensureCouponFromQuery($request),
            'offer' => $activeOffer,
            'effectivePrice' => $effectivePrice,
        ]);
    }

    // Procesa pago con PayPal (aplica cupón, suma puntos, crea pedido y marca vendido)
    public function processPaypalPayment(Request $request, Listing $listing)
    {
        abort_if($request->user()->id === $listing->user_id, 403);

        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'coupon_id' => ['nullable', 'integer'],
            'offer_id' => ['nullable', 'integer'],
        ]);

        $coupon = $this->validateCouponSelection($request, $request->input('coupon_id'));
        if ($request->filled('coupon_id') && !$coupon) {
            return back()->withErrors(['coupon_id' => 'Cupón no válido.'])->withInput();
        }

        $offer = $this->resolveAcceptedOffer($request, $listing, $request->input('offer_id'));

        $basePrice = $this->effectivePrice($listing, $offer);
        $discount = 0;
        if ($coupon) {
            $discount = min($coupon->value, $basePrice);
            $coupon->update(['redeemed_at' => now()]);
        }
        $finalAmount = max(0, $basePrice - $discount);

        $points = (int) floor($finalAmount);
        if ($points > 0) {
            $request->user()->increment('points', $points);
        }

        $this->recordOrder($request->user()->id, $listing, $finalAmount);
        $listing->update(['status' => 'sold']);

        return redirect()->route('listings.index')->with('status', 'Pago con PayPal registrado (demo).');
    }

    // Muestra formulario de creación de anuncio
    public function create(Request $request)
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        return view('listings.create', compact('categories'));
    }

    // Muestra formulario de edición de un anuncio propio
    public function edit(Request $request, Listing $listing)
    {
        abort_unless($request->user()->id === $listing->user_id, 403);

        $categories = Category::select('id', 'name')->orderBy('name')->get();

        return view('listings.edit', [
            'listing' => $listing,
            'categories' => $categories,
        ]);
    }

    // Crea un anuncio y guarda imágenes
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'condition' => ['required', Rule::in(['new', 'like_new', 'used', 'for_parts'])],
            'status' => ['nullable', Rule::in(['available', 'reserved', 'sold'])],
            'city' => ['nullable', 'string', 'max:120'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'images' => ['required', 'array', 'min:1', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $listing = new Listing($validated);
        $listing->user_id = $request->user()->id;
        $listing->save();

        // Guardar imágenes si se han enviado
        if ($request->hasFile('images')) {
            $disk = 'public';
            $basePath = 'listings/'.$listing->id;
            $nextOrder = 0;

            foreach ($request->file('images') as $file) {
                if (!$file->isValid()) { continue; }
                $path = $file->store($basePath, $disk);
                $thumbPath = $this->makeThumbnail($file->getRealPath(), $basePath, $disk);

                $image = new ListingImage([
                    'path' => $path,
                    'thumb_path' => $thumbPath,
                    'order' => $nextOrder++,
                ]);
                $listing->images()->save($image);
            }
        }

        return $request->wantsJson()
            ? response()->json($listing->fresh(), 201)
            : redirect()->route('listings.index')->with('status', 'Anuncio creado');
    }

    // Genera miniatura para una imagen subida
    private function makeThumbnail(string $sourcePath, string $basePath, string $disk): ?string
    {
        try {
            if (!extension_loaded('gd')) {
                return null;
            }

            $data = @file_get_contents($sourcePath);
            if ($data === false) {
                return null;
            }

            $img = @imagecreatefromstring($data);
            if (!$img) {
                return null;
            }

            $width = imagesx($img);
            $height = imagesy($img);

            $max = 600;
            $scale = min(1.0, $max / max($width, $height));
            $newW = (int) max(1, round($width * $scale));
            $newH = (int) max(1, round($height * $scale));

            $thumb = imagecreatetruecolor($newW, $newH);
            imagecopyresampled($thumb, $img, 0, 0, 0, 0, $newW, $newH, $width, $height);

            ob_start();
            imagejpeg($thumb, null, 80);
            $jpeg = ob_get_clean();

            imagedestroy($thumb);
            imagedestroy($img);

            if ($jpeg === false) {
                return null;
            }

            $filename = 'thumbs/'.uniqid('', true).'.jpg';
            $thumbPath = rtrim($basePath, '/').'/'.$filename;

            Storage::disk($disk)->put($thumbPath, $jpeg, 'public');

            return $thumbPath;
        } catch (\Throwable $e) {
            return null;
        }
    }

    // Actualiza un anuncio propio
    public function update(Request $request, Listing $listing)
    {
        abort_unless($request->user()->id === $listing->user_id, 403);

        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'title' => ['sometimes', 'required', 'string', 'max:200'],
            'description' => ['sometimes', 'nullable', 'string'],
            'price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'condition' => ['sometimes', 'required', Rule::in(['new', 'like_new', 'used', 'for_parts'])],
            'status' => ['sometimes', 'required', Rule::in(['available', 'reserved', 'sold'])],
            'city' => ['sometimes', 'nullable', 'string', 'max:120'],
            'lat' => ['sometimes', 'nullable', 'numeric'],
            'lng' => ['sometimes', 'nullable', 'numeric'],
        ]);

        $listing->fill($validated)->save();

        return $request->wantsJson()
            ? response()->json($listing)
            : redirect()->route('listings.show', $listing)->with('status', 'Anuncio actualizado');
    }

    // Elimina un anuncio propio
    public function destroy(Request $request, Listing $listing)
    {
        abort_unless($request->user()->id === $listing->user_id, 403);
        $listing->delete();

        return $request->wantsJson()
            ? response()->json(['deleted' => true])
            : redirect()->route('listings.index')->with('status', 'Anuncio eliminado');
    }

    // Valida cupón disponible del usuario autenticado
    private function validateCouponSelection(Request $request, $couponId): ?UserCoupon
    {
        if (empty($couponId)) {
            return null;
        }

        return $request->user()->coupons()->whereNull('redeemed_at')->whereKey($couponId)->first();
    }

    // Recupera cupón desde querystring
    private function ensureCouponFromQuery(Request $request): ?UserCoupon
    {
        $couponId = $request->query('coupon');
        return $this->validateCouponSelection($request, $couponId);
    }

    // Obtiene una oferta aceptada para el comprador actual
    private function resolveAcceptedOffer(Request $request, Listing $listing, $offerId = null): ?Offer
    {
        if ($offerId) {
            return $listing->offers()
                ->where('id', $offerId)
                ->where('user_id', $request->user()->id)
                ->where('status', 'accepted')
                ->first();
        }

        return $listing->offers()
            ->where('user_id', $request->user()->id)
            ->where('status', 'accepted')
            ->latest()
            ->first();
    }

    // Calcula el precio efectivo
    private function effectivePrice(Listing $listing, ?Offer $offer): float
    {
        if ($offer && $offer->amount > 0) {
            return (float) $offer->amount;
        }
        return max(0, (float) ($listing->price ?? 0));
    }

    // Registra un pedido con estado inicial
    private function recordOrder(int $buyerId, Listing $listing, float $amount): void
    {
        Order::firstOrCreate(
            ['listing_id' => $listing->id],
            [
                'user_id' => $buyerId,
                'seller_id' => $listing->user_id,
                'status' => 'no_enviado',
            ]
        );
    }
}
