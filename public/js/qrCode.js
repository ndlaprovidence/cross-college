function onScanSuccess(identifiant) {
    document.getElementById('result').innerHTML = '<span class="result">' + identifiant + '</span>';
    let sender = new XMLHttpRequest();
    sender.open('GET', 'https://benjamin.pro.ndlpavranches.fr/reception/?identifiant=' + identifiant);
    sender.send();
    //alert(identifiant); 
}

function onScanError(errorMessage) {
    //handle scan error
}
var html5QrcodeScanner = new Html5QrcodeScanner(
    "reader", {
        fps: 10,
        qrbox: 250
    }); 
html5QrcodeScanner.render(onScanSuccess, onScanError);