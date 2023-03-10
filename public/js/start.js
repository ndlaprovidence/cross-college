function start() {
    let verif = confirm("Etes-vous sûr de vouloir lancer la course");
    if (verif ==  true) {
        let sender = new XMLHttpRequest();
        sender.open('GET', 'http://localhost:8000/reception/?start=1');
        sender.send();
        window.location.reload();
    } 
}