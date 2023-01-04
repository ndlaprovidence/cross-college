function start() {
    let verif = confirm("Etes-vous sûr de vouloir lancer la course");
    if (verif ==  true) {
        let sender = new XMLHttpRequest();
        sender.open('GET', 'localhost:8000/ranking/index.html.twig/?start=1');
        sender.send();
    } 
}