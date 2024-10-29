let articleForm = document.querySelector('.article-form');
let publishButton = document.querySelector('#publish');
let alertZone = document.querySelector('.alert');

publishButton.addEventListener('click', async function (event) {

    event.preventDefault();
    let elementToVisualize = document.querySelector('#editor > div.ql-editor')

    let articleContent = elementToVisualize.innerHTML


    articleContent = articleContent.replace('&lt;', '<');
    articleContent = articleContent.replace('&gt;', '>');
    

    const raw = JSON.stringify({
        "title" : document.querySelector('#title').value,
        "content" : articleContent,
        "isPublished" : true 
    })

    if (articleContent.includes('<script')){
        printDanger(alertZone, 'Attention : ', 'Certain élément HTML sont complètement interdit. Enlever-les pour pour pouvoir enregistrer cet article.')
    }

    let request = {
        method: articleSubmitMethod,
        headers:{'Content-Type': 'application/json'},
        body: raw
    };

    await fetch(articleSubmitUrl, request)
    .then(response => { 

        if (response.status == 201) {

            printSuccess(alertZone, 'Information :', 'Article a bien été publié')

        } else if (response.status < 400){

            printWarning(alertZone, 'Erreur ' +response.status, 'Une évènement innatendu s\'est produit durant l\'exécution. L\'article ne s\'est peut-être pas enregistré.')

        }
    })
    .catch(error => {

        printDanger(alertZone, 'Attention ' +response.status, error)

   })
})

function printDanger(alertZone, introMessage, message)
{
    alertZone.classList.add('alert-danger')
    alertZone.classList.remove('alert-success')
    alertZone.classList.remove('alert-warning')
    printAlert(alertZone, introMessage, message);
}

function printSuccess(alertZone, introMessage, message)
{
    alertZone.classList.add('alert-success');
    alertZone.classList.remove('alert-danger');
    alertZone.classList.remove('alert-warning');
    printAlert(alertZone, introMessage, message);
}

function printWarning(alertZone, introMessage, message)
{
    alertZone.classList.add('alert-warning');
    alertZone.classList.remove('alert-danger');
    alertZone.classList.remove('alert-success');
    printAlert(alertZone, introMessage, message);
}

function printAlert(alertZone, introMessage, message) 
{

    alertZone.classList.remove('d-none')
    alertZone.querySelector('.alert-type').innerHTML = introMessage
    alertZone.querySelector('.alert-message').innerHTML = message

}