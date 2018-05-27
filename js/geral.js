$(document).ready(function() {	
    $('.fullscreen').on('click', function() {
        if ((document.fullScreenElement && document.fullScreenElement !== null) ||    
           (!document.mozFullScreen && !document.webkitIsFullScreen)) {
        if (document.documentElement.requestFullScreen) {  
          document.documentElement.requestFullScreen();  
        } else if (document.documentElement.mozRequestFullScreen) {  
          document.documentElement.mozRequestFullScreen();  
        } else if (document.documentElement.webkitRequestFullScreen) {  
          document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);  
        }  
      } else {  
        if (document.cancelFullScreen) {  
          document.cancelFullScreen();  
        } else if (document.mozCancelFullScreen) {  
          document.mozCancelFullScreen();  
        } else if (document.webkitCancelFullScreen) {  
          document.webkitCancelFullScreen();  
        }  
      }  
    })
    
    setInterval(function(){
        $.ajax({
            url: 'ajax/count-contatos-importacao.php',
            dataType: 'html',
            type: 'POST',
            statusCode: {
                        404: function() {
                            n = 1;
                                alert( "Arquivo não encontrado" );
                        },
                        500: function() {
                            n = 1;
                                alert( "Ocorreu um erro interno" );
                        }
            },
            success: function(data){
                            alert('success');
                if(parseInt(data) > 0)
                    $("#header-notification .badge").text(data);
                else
                    $("#header-notification .badge").text('');

            },
            beforeSend: function(){
                        alert('before');
                
            },
            error: function(){
                            alert('error');
                if(n==0)
                    alert('Ocorreu um erro!');
            }
         });
    }, 30 * 1000)
    
    $('.dropdown-toggle-notification').unbind();
    $('.dropdown-toggle-notification').on('click', function(e){
        e.preventDefault();
//        var janela = abre_tela_full('../modulos/customizacoes/coaching/fale_conosco/c_coaching_tela_importacao_fale_conosco.php','c_coaching_importar_fs',750,550,1);
        alert('Em breve');
    })

    $('.loadContact').on('click', function(e){
        e.preventDefault();
        loadContacts(dtbr2en($('input[name=daterangepicker_start]').val()),dtbr2en($('input[name=daterangepicker_end]').val()));

    })

    $('.loadInscricoes').on('click', function(e){
        e.preventDefault();
        loadInscricoes(dtbr2en($('input[name=daterangepicker_start]').val()),dtbr2en($('input[name=daterangepicker_end]').val()));
    })
    
});

function lastDate(month,year){
    if(typeof month == 'undefined')
        month = new Date().getMonth()+1;
    if(typeof year == 'undefined')
        year = new Date().getFullYear();
    return (new Date(year, month, 0)).getDate();
}

function loadContacts(from, to){
    if(typeof(from) != 'undefined' && from != null && typeof(to) != 'undefined' && to != null){
        data = {'from': from, 'to': to};
    }else{
        data = {};
    }
    var loadContacts = $.ajax({
                        url: 'http://127.0.0.1/oasis-sbc/adm/ajax/count-contatos.php',
                        dataType: 'html',
                        type: 'POST',
                        data: data,
                        statusCode: {
                                    404: function() {
                                        n = 1;
                                            alert( "Arquivo não encontrado" );
                                    },
                                    500: function() {
                                        n = 1;
                                            alert( "Ocorreu um erro interno" );
                                    }
                        },
                        beforeSend: function(data){
                            alert('before');
                            var totalContatos = jQuery('.totalContatos').parents(".panel-body");
                            App.blockUI(totalContatos);
                        },
                        success: function(data){
                            $('.totalContatos').text(data);
                        },
                        complete: function(data){
                            var totalContatos = jQuery('.totalContatos').parents(".panel-body");
                            App.unblockUI(totalContatos);  
                        },
                        error: function(){
                            alert('erro aki');
                            if(n==0)
                                alert('Ocorreu um erro!');
                        }
                     });
}

function loadInscricoes(from, to){
    if(typeof(from) != 'undefined' && from != null && typeof(to) != 'undefined' && to != null){
        data = {'from': from, 'to': to};
    }else{
        data = {};
    }
    var loadInscricoes = $.ajax({
                        url: 'http://127.0.0.1/oasis-sbc/adm/ajax/count-inscricoes.php',
                        dataType: 'html',
                        type: 'POST',
                        data: data,
                        statusCode: {
                                    404: function() {
                                        n = 1;
                                            alert( "Arquivo não encontrado" );
                                    },
                                    500: function() {
                                        n = 1;
                                            alert( "Ocorreu um erro interno" );
                                    }
                        },
                        beforeSend: function(data){
                            var totalInscricoes = jQuery('.totalInscricoes').parents(".panel-body");
                            App.blockUI(totalInscricoes);
                        },
                        success: function(data){
                            $('.totalInscricoes').text(data);
                        },
                        complete: function(data){
                            var totalInscricoes = jQuery('.totalInscricoes').parents(".panel-body");
                            App.unblockUI(totalInscricoes);  
                        },
                        error: function(){
                            if(n==0)
                                alert('Ocorreu um erro!');
                        }
                     });
}

function loadGoals(seletor, type, month, year){
    if(typeof(type) == 'undefined' || type == null)
        type = '';

    if(typeof(month) == 'undefined' || month == null)
        month = '';

    if(typeof(year) == 'undefined' || year == null)
        year = '';

    data = {'type': type, 'month': month, 'year': year};

    var loadGoals = $.ajax({
                        url: 'http://127.0.0.1/oasis-sbc/adm/ajax/count-meta.php',
                        dataType: 'html',
                        type: 'POST',
                        data: data,
                        statusCode: {
                                    404: function() {
                                        n = 1;
                                            alert( "Arquivo não encontrado" );
                                    },
                                    500: function() {
                                        n = 1;
                                            alert( "Ocorreu um erro interno" );
                                    }
                        },
                        beforeSend: function(data){
                            App.blockUI(jQuery('.quick-pie'));
                        },
                        success: function(data){
                            $(seletor).attr('goal', data);
                        },
                        complete: function(data){

                        },
                        error: function(){
                            if(n==0)
                                alert('Ocorreu um erro!');
                        }
                     });
}

function loadProject(seletor, file, title){
    var loadGoals = $.ajax({
                        url: 'http://127.0.0.1/oasis-sbc/adm/ajax/'+file+'.php',
                        dataType: 'html',
                        type: 'POST',
                        statusCode: {
                                    404: function() {
                                        n = 1;
                                            alert( "Arquivo não encontrado" );
                                    },
                                    500: function() {
                                        n = 1;
                                            alert( "Ocorreu um erro interno" );
                                    }
                        },
                        beforeSend: function(data){
                            App.blockUI(jQuery('.quick-pie'));
                        },
                        success: function(data){
                            $('#'+seletor).attr('valor', data);
                        },
                        complete: function(data){
                            App.unblockUI(jQuery('.quick-pie'));  
                            var g1 = new JustGage({
                                id: seletor, 
                                value: +$('#'+seletor).attr('valor'), 
                                min: 0,
                                max: +$('#'+seletor).attr('goal'),
                                title: title,
                                shadowOpacity: 1,
                                shadowSize: 0,
                                shadowVerticalOffset: 2,
                                levelColors: [Theme.colors.red, Theme.colors.yellow, Theme.colors.green]
                              });
                        },
                        error: function(){
                            if(n==0)
                                alert('Ocorreu um erro!');
                        }
                     });
}



