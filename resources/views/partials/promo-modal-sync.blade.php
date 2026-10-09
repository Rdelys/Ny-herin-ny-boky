<style>
    .pd-old-price{
        font-size: .9rem;
        font-weight: 500;
        color: #9c8b7d;
        text-decoration: line-through;
        text-decoration-thickness: 1.5px;
    }
    .pd-promo-badge{
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .03em;
        padding: 4px 10px;
        border-radius: 999px;
        background: rgba(179,38,30,.12);
        color: #b3261e;
        white-space: nowrap;
    }
    .pd-old-price[hidden],
    .pd-promo-badge[hidden]{ display: none !important; }
</style>

<script>
(function(){
    function fmt(n){ return Math.round(Number(n)).toLocaleString('fr-FR') + ' Ar'; }

    function ensure(){
        var price = document.getElementById('orderUnitPrice');
        if (!price) return null;

        var old = document.getElementById('orderOldPrice');
        if (!old) {
            old = document.createElement('s');
            old.id = 'orderOldPrice';
            old.className = 'pd-old-price';
            old.hidden = true;
            price.insertAdjacentElement('afterend', old);
        }
        var badge = document.getElementById('orderPromoBadge');
        if (!badge) {
            badge = document.createElement('span');
            badge.id = 'orderPromoBadge';
            badge.className = 'pd-promo-badge';
            badge.hidden = true;
            old.insertAdjacentElement('afterend', badge);
        }
        return { old: old, badge: badge };
    }

    document.addEventListener('click', function(e){
        var trigger = e.target.closest ? e.target.closest('[data-book-order]') : null;
        if (!trigger) return;

        var els = ensure();
        if (!els) return;

        var oldPrice = parseFloat(trigger.getAttribute('data-book-old-price'));
        var label = trigger.getAttribute('data-book-promo-label');

        if (oldPrice > 0 && label) {
            els.old.textContent = fmt(oldPrice);
            els.old.hidden = false;
            els.badge.textContent = label;
            els.badge.hidden = false;
        } else {
            els.old.hidden = true;
            els.badge.hidden = true;
        }
    }, true);
})();
</script>