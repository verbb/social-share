document.addEventListener('click', (event) => {
    if (
        event.defaultPrevented ||
        event.button !== 0 ||
        event.metaKey ||
        event.ctrlKey ||
        event.shiftKey ||
        event.altKey ||
        !(event.target instanceof Element)
    ) {
        return;
    }

    const printButton = event.target.closest('[data-social-share-print]');

    if (printButton) {
        event.preventDefault();
        window.print();
        return;
    }

    const shareLink = event.target.closest('[data-social-share-popup]');

    if (!shareLink || !shareLink.href) {
        return;
    }

    const popup = window.open('', 'ss_share_dialog', 'width=626,height=436');

    if (!popup) {
        return;
    }

    popup.opener = null;
    popup.location.href = shareLink.href;
    event.preventDefault();
});
