function printRecept() {
    window.print();
}

function toggleInputFields(ingredient) {
    var checkbox = document.getElementById('ingredient_' + ingredient);
    var fields = document.getElementById('fields_' + ingredient);
    if (checkbox.checked) {
        fields.style.display = 'block';
    } else {
        fields.style.display = 'none';
    }
}

function toggleIngredientFields(ingredient) {
    console.log ("werkt");
    let extraFields = document.getElementById("extraFields_" + ingredient);
    let checkbox = document.getElementById("ingredient_" + ingredient);

    if (checkbox.checked) {
        extraFields.style.display = "block";
    } else {
        extraFields.style.display = "none";
    }
}


document.addEventListener('DOMContentLoaded', function() {
    // Verkrijg de knoppen
    const smallBtn = document.getElementById('small-text');
    const mediumBtn = document.getElementById('medium-text');
    const largeBtn = document.getElementById('large-text');
    
    // Functie om lettergrootte in te stellen
    function setFontSize(size) {
        // Verwijder de bestaande lettergrootte klassen van de body
        document.body.classList.remove('font-small', 'font-medium', 'font-large');
        
        // Voeg de juiste lettergrootte klasse toe
        switch(size) {
            case 'small':
                document.body.classList.add('font-small');
                break;
            case 'medium':
                document.body.classList.add('font-medium');
                break;
            case 'large':
                document.body.classList.add('font-large');
                break;
        }
    }

    // Event listeners voor de knoppen
    smallBtn.addEventListener('click', function() {
        setFontSize('small');
    });

    mediumBtn.addEventListener('click', function() {
        setFontSize('medium');
    });

    largeBtn.addEventListener('click', function() {
        setFontSize('large');
    });
});
