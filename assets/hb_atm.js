

window.onload=function(){
    const hbtimeElement  = document.querySelector('#hb-atm-time'),
          hbtextElement  = document.querySelector('#hb-atm-text'),
          hbATMElement  = document.querySelector('#hbATM');

    today = new Date(+new Date() + 8 * 3600 * 1000);
    if(!hbtimeElement.value){

        hbtimeElement.value=today.toISOString().substring(0, 10);
    }
    hbtimeElement.setAttribute('max',today.toISOString().substring(0, 10));

    hbtextElement.addEventListener('keydown', (event) => {

        if(event.target.value.length>=5){
            hbtextElement.value = hbtextElement.value.substring(0,4);
        }
    });

    hbATMElement.addEventListener('submit', (event) => {
        if(hbtextElement.value.length!=5){
            alert(HBATM5numbers);
            event.preventDefault();
            return;
        }
        if(isNaN(hbtextElement.value)){
            alert(HBATMbeanumbers);
            event.preventDefault();
            return;
        }
        if(!hbtimeElement){
            event.preventDefault();
            return;
        }

    });

}

