function copyToClipboard(elem) {
    const element = document.querySelector(elem);
    element.select();
    element.setSelectionRange(0, 99999);
    document.execCommand('copy');
    toastr.success('Copy to Clipboard Success!');
}