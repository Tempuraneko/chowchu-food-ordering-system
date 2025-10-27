$(document).ready(function () {
    const dropArea = $('.drop-section');
    const listSection = $('.list-section');
    const listContainer = $('.list');
    const fileSelector = $('.file-selector');
    const fileSelectorInput = $('.file-selector-input');

    fileSelector.on('click', (e) => {
        e.preventDefault(); 
        fileSelectorInput.click();
    });

    fileSelectorInput.on('change', function (e) {
        e.preventDefault(); 
        $.each(fileSelectorInput[0].files, (index, file) => {
            if (typeValidation(file.type)) {
                uploadFile(file);
            }
        });
    });

    dropArea.on('dragover', (e) => {
        e.preventDefault();
        $.each(e.originalEvent.dataTransfer.items, (index, item) => {
            if (typeValidation(item.type)) {
                dropArea.addClass('drag-over-effect');
            }
        });
    });

    dropArea.on('dragleave', () => {
        dropArea.removeClass('drag-over-effect');
    });

    dropArea.on('drop', (e) => {
        e.preventDefault(); 
        dropArea.removeClass('drag-over-effect');
        const items = e.originalEvent.dataTransfer.items || e.originalEvent.dataTransfer.files;
        $.each(items, (index, item) => {
            const file = item.kind === 'file' ? item.getAsFile() : item;
            if (typeValidation(file.type)) {
                uploadFile(file);
            }
        });
    });

    function typeValidation(type) {
        const splitType = type.split('/')[0];
        return type === 'application/pdf' || splitType === 'image' || splitType === 'video';
    }

    function uploadFile(file) {
        listSection.show();
    
        const li = $('<li class="in-prog"></li>');
        li.html(`
            <div class="col">
                <!-- No fake image/icon is added here -->
            </div>
            <div class="col">
                <div class="file-name">
                    <div class="name">${file.name}</div>
                    <span>0%</span>
                </div>
                <div class="file-progress">
                    <span></span>
                </div>
                <div class="file-size">${(file.size / (1024 * 1024)).toFixed(2)} MB</div>
            </div>
            <div class="col">
                <svg xmlns="http://www.w3.org/2000/svg" class="cross" height="20" width="20"><path d="m5.979 14.917-.854-.896 4-4.021-4-4.062.854-.896 4.042 4.062 4-4.062.854.896-4 4.062 4 4.021-.854.896-4-4.063Z"/></svg>
                <svg xmlns="http://www.w3.org/2000/svg" class="tick" height="20" width="20"><path d="m8.229 14.438-3.896-3.917 1.438-1.438 2.458 2.459 6-6L15.667 7Z"/></svg>
            </div>
        `);
        listContainer.prepend(li);
    
        if (file.type.startsWith('image')) {
            const reader = new FileReader();
            reader.onload = function (e) {

                const imgPreview = $('<img>').attr('src', e.target.result).attr('alt', 'Image preview').addClass('image-preview');
                li.find('.col:first').append(imgPreview); 
            };
            reader.readAsDataURL(file); 
        }
    
        // Uploading the file
        const http = new XMLHttpRequest();
        const data = new FormData();
        data.append('file', file);
    
        http.onload = () => {
            li.addClass('complete').removeClass('in-prog');
        };
    
        http.upload.onprogress = (e) => {
            const percent_complete = (e.loaded / e.total) * 100;
            li.find('span').first().text(Math.round(percent_complete) + '%');
            li.find('span').last().css('width', percent_complete + '%');
        };
    
        http.open('POST', 'adminProductC.php', true);
        http.send(data);
    
        li.find('.cross').on('click', () => http.abort());
        http.onabort = () => li.remove();
    }
    

    function iconSelector(type) {
        const splitType = type.split('/')[0] === 'application' ? type.split('/')[1] : type.split('/')[0];
        return splitType + '.png';
    }
});

