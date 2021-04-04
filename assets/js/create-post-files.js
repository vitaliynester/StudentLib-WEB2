$('#files').on('change', function () {
    var arrayFiles = this.files, // массив с выбранными фалами
        formItem = this.parentNode, // родительский элемент, для того чтобы вставить список с файлами
        listFiles = document.createElement('ul'), // список с файлами
        li = ''; // файлы
    // Если список с файлами уже вставлен в ДОМ, то удаляем его
    if (formItem.querySelector('.list-files')) {
        formItem.querySelector('.list-files').remove();
    }
    listFiles.className = 'list-files'; // добавим класс, чтобы было удобнее стилять
    for (var i = 0; i < arrayFiles.length; i++) {
        li += '<li>' + arrayFiles[i].name + '</li>'; // <li>Имя файла</li>
    }
    listFiles.innerHTML = li;
    formItem.appendChild(listFiles);
});