function cetakStruk(url) {
    const showprint = window.open(url, "_blank", "height=600, width=400");

    showprint.addEventListener("load", function () {
        showprint.print();
        setTimeout(() => {
            showprint.close();
        }, 4000);
    });

    return false;
}

window.cetakStruk = cetakStruk;
