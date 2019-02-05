enrl_code = document.getElementsByClassName("copy_code");

for(let i = 0; i < enrl_code.length; i++) {
    enrl_code[i].onclick = function () {
        document.execCommand("copy");
    };

    enrl_code[i].addEventListener("copy", function (event) {
        event.preventDefault();
        if (event.clipboardData) {
            event.clipboardData.setData("text/plain", event.srcElement.nextElementSibling.href);
        }
    });
}