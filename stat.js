let anneeButton = document.querySelector('.annee');
let polisButton = document.querySelector('.polis');

document.querySelector('.annee').addEventListener('click', function(){
    document.getElementById('body-annee').style.display = 'block';
    document.getElementById('body-polis').style.display = 'none';

    
    anneeButton.classList.add('selected')
    polisButton.classList.remove('selected')
    
})

document.querySelector('.polis').addEventListener('click', function(){
    document.getElementById('body-polis').style.display = 'block';
    document.getElementById('body-annee').style.display = 'none';

    polisButton.classList.add('selected')
    anneeButton.classList.remove('selected')
})

