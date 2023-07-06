function start() {
    let verif = confirm("Etes-vous sûr de vouloir lancer la course ?");
    if (verif) {
        let sender = new XMLHttpRequest();
        sender.open('GET', '/reception/?start=1');
        sender.send();
        // On empêche le rechargement de la page en utilisant la méthode preventDefault()
        event.preventDefault();
        // On raffraichit la page de scan
        window.open("/douchette");
    } 
}