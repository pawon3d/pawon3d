import moment from "moment";
import Pikaday from "pikaday";

window.moment = moment;
window.Pikaday = Pikaday;

function cetakStruk(url) {
    const showprint = window.open(url, "_blank", "height=600, width=400");

    showprint.addEventListener("load", function () {
        showprint.print();
        //if mobile device, close window after print
        if (navigator.userAgent.match(/(iPod|iPhone|iPad|Android)/)) {
            //
        } else {
            showprint.addEventListener("afterprint", function () {
                showprint.close();
            });
        }
    });

    return false;
}

window.cetakStruk = cetakStruk;
