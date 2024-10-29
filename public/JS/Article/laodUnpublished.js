let unpublishedArticle = [];
let unpublishedArticleZone = document.querySelector('#unpublishedArticleZone');

fetch('/api/unpublished/articles/', {
    method: 'GET',
    headers : {
        'Content-type' : 'application-json'
    }
})
.then(response => response.json())
.then(articles => {

    // {# <img src="..." class="card-img-top" alt="..."> #}

    articles.forEach(article => {

        unpublishedArticleZone.innerHTML += `<div class="card mx-3" style="min-width: 15em; max-width: 24em; ">
                <div class="card-body">
                    <h5 class="card-title">`+ article.title +`</h5>
                    <p class="card-text">` + article.content.slice(0, 100) + ` ...</p>
                    <a href="/articles/`+article.id+`/edition">
                        <button type="button" class="btn btn-primary"><i class="fa-solid fa-pen"></i> Editer </button>
                    </a>
                    <button class='btn btn-danger-outline border border-danger'><i class="fa-solid fa-trash-can"></i></button>

                </div>
            </div>`
        
    });

}).catch(error => {

    alert(error);

})

