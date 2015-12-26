/**
 * Created by Козлов Дмитрий on 26.12.2015.
 */
$(document).ready(function() {
    $('.comment-reply').click( function(){
        $('.comment-answer-block').removeClass('hidden');
        var block = $(this).parents('.comment')[0];
        var user = $('.user-href',block);
        $('.comment-to').text(user.text());
        $('.comment-to').attr('href', user.attr('href'));
        var value = $('.comment-id',block).val();
        $('.parent-comment-id').val(value);
    });

    $('.delete-comment-to').click( function(){
        $('.comment-answer-block').addClass('hidden');
        $('.parent-comment-id').val(null);
    });
});