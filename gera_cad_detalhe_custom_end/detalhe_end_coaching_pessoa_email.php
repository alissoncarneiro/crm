<?php
/*
 * is_pessoa.php
 * Autor: Alisson Carneiro
 * 19/03/2012 10:00
 gera_cad_detalhe_custom_end / detalhe_end_coaching_pessoa_email.php
 */
if($id_funcao == 'pessoa'){?>
 <script type="text/javascript">
     $(document).ready(function(){
        $("[name=2]").click(function() {
            var url = 'modulos/customizacoes/coaching/email_pessoa/envia_email.php?numreg=<?php echo $qry_cadastro['numreg'];?>';
            // show a spinner or something via css
            var dialog = $('<div style="display:none" class="loading"></div>').appendTo('body');
			dialog.attr("title",'Selecionar Modelo de impressão no e-mail');
            // open the dialog
            dialog.dialog({
                // add a close listener to prevent adding multiple divs to the document
                close: function(event, ui) {
                    // remove div with all data and events
                    dialog.remove();
                },
                modal: true,
                width: 900,
                height: 600
			});
            // load remote content
            dialog.load(
                url, 
                {}, // omit this param object to issue a GET request instead a POST request, otherwise you may provide post parameters within the object
                function (responseText, textStatus, XMLHttpRequest) {
                    // remove the loading class
                    dialog.removeClass('loading');
                }
            );
           //prevent the browser to follow the link
            return false;
        });
    });
    </script>
<?php
}

if($id_funcao == 'email_pessoa_enviados'){?>

<script language="javascript" src="tinymce/jscripts/tiny_mce/tiny_mce_gzip.php"></script>
<script language="javascript" type="text/javascript">
                tinyMCE.init({
                    mode : "exact",
                    elements : "edtemail_corpo",
                    theme : "advanced",
                    plugins : "style,layer,table,save,advhr,advimage,advlink,insertdatetime,preview,zoom,searchreplace,contextmenu,paste,fullscreen",
                    theme_advanced_buttons1_add_before : "newdocument,separator",
                    theme_advanced_buttons1_add : "fontselect,fontsizeselect,separator,forecolor,backcolor",
                    theme_advanced_buttons2_add : "separator,",
                    theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator",
                    theme_advanced_buttons3_add_before : "tablecontrols,separator,search,replace,separator,preview,fullscreen",
                    theme_advanced_toolbar_location : "top",
                    theme_advanced_toolbar_align : "left",
                    theme_advanced_statusbar_location : "bottom",
                    content_css : "",
                    plugi2n_insertdate_dateFormat : "%d-%m-%Y",
                    plugi2n_insertdate_timeFormat : "%H:%M:%S",
                    external_link_list_url : "example_link_list.js",
                    external_image_list_url : "example_image_list.js",
                    flash_external_list_url : "example_flash_list.js",
                    file_browser_callback : "fileBrowserCallBack",
                    paste_use_dialog : false,
                    theme_advanced_resizing : true,
                    theme_advanced_resize_horizontal : false,
                    theme_advanced_link_targets : "ifrm=ifrm",
                    paste_auto_cleanup_on_paste : true,
                    paste_convert_headers_to_strong : false,
                    paste_strip_class_attributes : "all",
                    paste_remove_spans : false,
                    force_br_newlines : true,
                    force_p_newlines : false,
                    forced_root_block : '',
                    language : "pt_br",
                    fullscreen_new_window : true,
                    fullscreen_settings : {
                        theme_advanced_path_location : "top"
                    }
                });

                function fileBrowserCallBack(field_name, url, type, win) {
                    // This is where you insert your custom filebrowser logic
                    alert("Filebrowser callback: field_name: " + field_name + ", url: " + url + ", type: " + type);

                   // Insert new URL, this would normaly be done in a popup
                    win.document.forms[0].elements[field_name].value = "someurl.htm";
               }
            </script>
<?php } ?>            