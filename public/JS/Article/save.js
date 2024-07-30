let save = document.querySelector('#save');
let publish = document.querySelector('#publish');
let titleEl = document.querySelector('#title');
let contentEl = document.querySelector('#editor > div');

save.addEventListener('click', async function(event){

    event.preventDefault();
    const title = titleEl.value;
    const content = contentEl.innerHTML;

    console.log(title)
    console.log(content)



    const raw = JSON.stringify({
        title : title,
        content : content,
        isPublished : false 
    })

    let request = {
        method: 'POST',
        headers:{'Content-Type': 'application/json'},
        body: raw
    };

    await fetch('http://127.0.0.1:8000/api/articles/', request)
    .then(response => { console.log(response)})
    .catch(error => {

        console.log(error)

   })

    // .then(data => {

    //      console.log(data)
    
    // })

});
