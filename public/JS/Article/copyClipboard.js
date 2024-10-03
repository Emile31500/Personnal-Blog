var copyClipboardBtns = document.querySelectorAll('.copy-clipboard-btn')
var clipboardContent = ''

copyClipboardBtns.forEach(copyClipboardBtn => {

    copyClipboardBtn.addEventListener('click', function (event) {

        event.preventDefault();

        clipboardContent = this.getAttribute('data-clipboard-content')
        message = this.getAttribute('data-clipboard-message')
        message = message.replace('<br>', '\n');

        navigator.clipboard.writeText(clipboardContent);
        alert('Info : \n' + message);
    })
    
});