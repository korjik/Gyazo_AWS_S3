$(document).ready(function(){
        $('#copy1').zclip({
                path:'scripts/ZeroClipboard.swf',
                copy:$('#url1').val(),
                afterCopy:function(){
                        $(this).after('<span class=copied></span>');
                        $(this).next('.copied').fadeOut('slow');
                },
        });
        $('#copy2').zclip({
                path:'scripts/ZeroClipboard.swf',
                copy:$('#url2').val(),
                afterCopy:function(){
                        $(this).after('<span class=copied></span>');
                        $(this).next('.copied').fadeOut('slow');
                },
        });
        $('#url1, #url2').click(function(){
                $(this).select();
        });
});
