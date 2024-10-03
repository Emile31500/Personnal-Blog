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

    visualizer.querySelector('.visualizer-article-title').innerHTML = document.querySelector('#title').value
    visualizer.querySelector('.visualizer-article-content').innerHTML = articleToVisualize
    visualizer.showModal();

})

closeVisualizer.addEventListener('click', function (event) {

    event.preventDefault();
    visualizer.close() 
    
})