var input = document.getElementById('txt_input');
    
input.addEventListener('invalid', function(e) {
    if(input.validity.valueMissing){
        e.target.setCustomValidity("テキストを入力してください"); 
    }
    input.addEventListener('input', function(e){
        e.target.setCustomValidity('');
    });
}, false);