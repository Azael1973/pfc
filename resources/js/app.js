import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Toggle de productos añadidos a favoritos via AJAX
document.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-favorite-toggle]');
    if (!btn) return;
    e.preventDefault();

    const storeUrl = btn.getAttribute('data-url-store');
    const destroyUrl = btn.getAttribute('data-url-destroy');
    const isFav = btn.getAttribute('data-favorited') === '1';

    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const url = isFav ? destroyUrl : storeUrl;
    const method = isFav ? 'DELETE' : 'POST';

    try {
        const res = await fetch(url, {
            method,
            headers: {
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        });

        if (res.redirected || res.status === 401) {
            window.location.href = '/login';
            return;
        }

        if (!res.ok && res.status !== 204) {
            console.error('Favorite toggle failed');
            return;
        }

        // Toggle UI
        const nowFav = !isFav;
        btn.setAttribute('data-favorited', nowFav ? '1' : '0');

        // Swap icon styles
        const filled = btn.querySelector('[data-fav-filled]');
        const outline = btn.querySelector('[data-fav-outline]');
        if (filled && outline) {
            filled.classList.toggle('hidden', !nowFav);
            outline.classList.toggle('hidden', nowFav);
        }

        // Optional: update title
        btn.title = nowFav ? 'Quitar de favoritos' : 'Añadir a favoritos';

        // Optional: update label text if present
        const label = btn.querySelector('[data-fav-label]');
        if (label) {
            label.textContent = nowFav ? 'Quitar de favoritos' : 'Añadir a favoritos';
            btn.classList.toggle('bg-pink-600', !nowFav);
            btn.classList.toggle('text-white', !nowFav);
            btn.classList.toggle('bg-gray-200', nowFav);
        }

        // Dispatch a global event so other pages (favorites) can react
        const listingId = btn.getAttribute('data-listing-id');
        window.dispatchEvent(new CustomEvent('favorite:toggled', { detail: { listingId, nowFav } }));
    } catch (err) {
        console.error(err);
    }
});

function applyTheme(theme) {
    if (theme === 'dark') document.documentElement.classList.add('dark');
    else document.documentElement.classList.remove('dark');
}

function toggleTheme() {
    const next = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
    localStorage.setItem('theme', next);
    applyTheme(next);
    const sun = document.getElementById('iconSun');
    const moon = document.getElementById('iconMoon');
    if (sun && moon) {
        const isDark = next === 'dark';
        moon.classList.toggle('hidden', isDark);
        sun.classList.toggle('hidden', !isDark);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    try {
        const initial = localStorage.getItem('theme') || 'light';
        applyTheme(initial);
        const isDark = initial === 'dark';
        const sun = document.getElementById('iconSun');
        const moon = document.getElementById('iconMoon');
        if (sun && moon) {
            moon.classList.toggle('hidden', isDark);
            sun.classList.toggle('hidden', !isDark);
        }
    } catch (e) {}
    const t1 = document.getElementById('themeToggle');
    const t2 = document.getElementById('themeToggleMobile');
    if (t1) t1.addEventListener('click', toggleTheme);
    if (t2) t2.addEventListener('click', toggleTheme);
});
// Actualizar la vista de favoritos en tiempo real
async function refreshFavoritesGrid() {
    const grid = document.querySelector('[data-favorites-grid]');
    if (!grid) return;
    try {
        const res = await fetch('/favorites', { headers: { 'Accept': 'application/json' } });
        if (!res.ok) return;
        const data = await res.json();
        const items = data.data || [];
        grid.innerHTML = items.map(item => {
            const img = (item.images && item.images.length) ? (item.images[0].thumb_path || item.images[0].path) : null;
            const imgTag = img ? `<img src="/storage/${img}" class=\"w-full h-full object-cover\">` : '<span class="text-gray-400">Sin imagen</span>';
            return `
            <div class="relative border rounded-md overflow-hidden" data-listing-card data-listing-id="${item.id}">
              <a href="/listings/${item.id}" class="block">
                <div class="aspect-square bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                  ${imgTag}
                </div>
              </a>
              <div class="p-3">
                <div class="text-gray-900 dark:text-gray-100 font-medium truncate">${item.title}</div>
                <div class="text-emerald-600 font-semibold">${item.price ? Number(item.price).toFixed(2) + ' €' : '—'}</div>
                <div class="text-sm text-gray-500">${item.city ?? '—'}</div>
              </div>
              <div class="absolute top-2 right-2">
                <button type="button"
                        data-favorite-toggle
                        data-listing-id="${item.id}"
                        data-favorited="1"
                        data-url-store="/listings/${item.id}/favorite"
                        data-url-destroy="/listings/${item.id}/favorite"
                        class="w-9 h-9 rounded-full bg-white/90 dark:bg-gray-800/90 border flex items-center justify-center hover:scale-105 transition"
                        title="Quitar de favoritos">
                  <svg data-fav-filled xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-pink-600"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 3 13.244 3 10.75 3 8.264 4.988 6.5 7.125 6.5c1.19 0 2.228.5 2.997 1.278L12 9.656l1.878-1.878A4.223 4.223 0 0116.875 6.5C19.012 6.5 21 8.264 21 10.75c0 2.494-1.688 4.61-3.989 6.757a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.218l-.022.012-.007.003a.75.75 0 01-.665 0z"/></svg>
                  <svg data-fav-outline xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5 text-pink-600 hidden"><path stroke-linecap="round" stroke-linejoin="round" d="M21 10.75c0 2.494-1.688 4.61-3.989 6.757a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.218l-.022.012-.007.003-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 3 13.244 3 10.75 3 8.264 4.988 6.5 7.125 6.5c1.19 0 2.228.5 2.997 1.278L12 9.656l1.878-1.878A4.223 4.223 0 0116.875 6.5C19.012 6.5 21 8.264 21 10.75z"/></svg>
                </button>
              </div>
            </div>`;
        }).join('');
    } catch (e) { console.error(e); }
}

window.addEventListener('favorite:toggled', (ev) => {
    const grid = document.querySelector('[data-favorites-grid]');
    if (!grid) return;
    const { listingId, nowFav } = ev.detail || {};
    if (!listingId) return;
    const card = grid.querySelector(`[data-listing-card][data-listing-id="${listingId}"]`);
    if (card && !nowFav) {
        // Quitar el producto de la vista de favoritos cuando se desmarque
        card.remove();
        return;
    }
    // En cualquier otro caso, simplemente refrescar el grid
    refreshFavoritesGrid();
});
