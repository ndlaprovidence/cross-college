function start() {
    let verif = confirm("Etes-vous sûr de vouloir lancer la course");
    if (verif ==  true) {
        let sender = new XMLHttpRequest();
        sender.open('GET', 'https://benjamin.pro.ndlpavranches.fr/reception/?start=1');
        sender.send();
    } 
}