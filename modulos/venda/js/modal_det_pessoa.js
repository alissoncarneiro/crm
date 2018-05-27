$(document).ready(function(){
    $('#btn_det_pessoa').qtip( {
        content: {
            method: 'post',
            url: 'modal_det_pessoa.php',
            data: 'numreg='+$("#edtid_pessoa").val(),
            text: '<img src="img/loading.gif" alt="Carregando..." />',
            title: {
                text: 'Informa&ccedil;&otilde;es do Cliente',
                button: 'Fechar'
            }
        },
        
        position: {
            target: $(document.body), // Position it via the document body...
            corner: 'center' // ...at the center of the viewport
        },
        show: {
            
            when: 'click', // Show it on click
            solo: true // And hide all other tooltips
        },
        hide: false,
        style: {
            width: 500,
            padding: '14px',
            background:'#FFFFFF',
            color:'#000000',
            border: {
                width: 9,
                radius: 9,
                color: '#666666'
            },
            name: 'blue'
        },
        api: {
            beforeShow: function()
            {
                // Fade in the modal "blanket" using the defined show speed
                $('#qtip-blanket').fadeIn(this.options.show.effect.length);
                this.loadContent(this.options.content.url, this.options.content.data, this.options.content.method);
            },
            beforeHide: function()
            {
                // Fade out the modal "blanket" using the defined hide speed
                $('#qtip-blanket').fadeOut(this.options.hide.effect.length);
            }
        }
    });
});