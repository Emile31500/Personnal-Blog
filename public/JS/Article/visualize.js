let visualizeBtn = document.querySelector('.visualize-btn');
let visualizer = document.querySelector('#visualizer');
let closeVisualizer = visualizer.querySelector('.close')



visualizeBtn.addEventListener('click', function (event) {
    
    event.preventDefault();
    let selectorElementToVisualize =  this.getAttribute('data-target-visualize')
    let elementToVisualize = document.querySelector(selectorElementToVisualize)

    let articleToVisualize = elementToVisualize.innerHTML


    articleToVisualize = articleToVisualize.replace('&lt;', '<');
    articleToVisualize = articleToVisualize.replace('&gt;', '>');

    if (articleToVisualize.includes('<script'))
    {
        visualizer.querySelector('.visualizer-article-content').innerHTML =  '<div class="alert alert-danger"><b class="alert-type">Attention : </b><p class="alert-message">Certain élément HTML sont complètement interdit. Enlever-les pour pour pouvoir enregistrer cet article.</p></div>'
        visualizer.showModal();
        return false;
    }

    visualizer.querySelector('.visualizer-article-title').innerHTML = document.querySelector('#title').value
    visualizer.querySelector('.visualizer-article-content').innerHTML = articleToVisualize
    visualizer.showModal();

})

closeVisualizer.addEventListener('click', function (event) {

    event.preventDefault();
    visualizer.close() 
    
})