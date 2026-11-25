'use strict';
document.addEventListener('error', event => {
    const image = event.target;
    if (!(image instanceof HTMLImageElement) || image.dataset.fallback) return;
    if (!image.closest('.card-img, .detail-img')) return;
    image.dataset.fallback = 'true';
    image.src = new URL('../images/room-1.svg', document.baseURI).href;
}, true);

