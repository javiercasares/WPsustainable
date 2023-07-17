(function (blocks, element) {
    var el = element.createElement;
    const currentDomain = window.location.hostname;

    blocks.registerBlockType('wpsustainable/greenbadge', {
        edit: function () {
            return el('p', { className: 'wpsustainable-greenbadge' }, [
                el('img', { src: 'https://api.thegreenwebfoundation.org/greencheckimage/' + currentDomain + '?nocache=true' }),
            ]);
        },
        save: function () {
            return el('figure', { className: 'wpsustainable-greenbadge' }, [
                el('img', { src: 'https://api.thegreenwebfoundation.org/greencheckimage/' + currentDomain + '?nocache=true' }),
            ]);
        },
    });
})(window.wp.blocks, window.wp.element);