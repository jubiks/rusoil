function listRowAdd() {
    var container = $('.list-items .list-body');
    var template = $('script.list-row-template').html();
    var number = parseInt(container.data('rows')) + 1;
    template = template.replace(/\{\{num\}\}/g, number);
    $(template).appendTo(container);
    container.data('rows', number);
    console.log(number);
}

function listRowDel(number) {
    $('.list-items .list-body .list-body-row[data-row=' + number + ']').remove();
}