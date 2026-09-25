const ZIELONE_POLE = 50;
const RAMKA = 2;
const KOLUMNA = 100;
const WIERSZ = 80;
const STRZALKA = 30;
const SZEROKOSC_TABELI = 700;
const WYSOKOSC_ZADANIA = 50;

const LICZBA_SCIEZEK = 5;

let pozycjaStartu = 0;
let licznikZadan = 0;
let osobyNaSciezkach = {};

function $$(selector) {
    return document.getElementById(selector);
}

function userAdd() {
    let user = $$("user").value.trim();
    if (user === "") return;

    let userList = $$("userList");
    let option = document.createElement("option");

    option.text = user;
    option.value = userList.options.length + 1;

    userList.appendChild(option);

    userList.value = option.value;

    $$("user").value = "";

    odswiezPanelOsob();
}

function createTableNet() {
    let table = $$("table");
    for (let x = KOLUMNA; x < SZEROKOSC_TABELI; x += KOLUMNA) {
        let div = document.createElement("div");
        div.className = "liniaPionowa";
        div.style.left = x + "px";
        table.appendChild(div);
    }
    for (let y = WIERSZ; y < 400; y += WIERSZ) {
        let div = document.createElement("div");
        div.className = "liniaPozioma";
        div.style.top = y + "px";
        table.appendChild(div);
    }
}

function arrowMove() {
    let path = Number($$("path").value);
    let srodek = RAMKA + (path - 1) * WIERSZ + WIERSZ / 2;
    $$("leftArrow").style.top = (srodek - STRZALKA / 2) + "px";
}

function ustawGornaStrzalke() {
    let srodek = ZIELONE_POLE + RAMKA + pozycjaStartu;
    $$("topArrow").style.left = (srodek - STRZALKA / 2) + "px";
}

function ruszTym(zdarzenie) {
    let pole = $$("topTableBorder");
    let prostokat = pole.getBoundingClientRect();

    let x = zdarzenie.clientX - prostokat.left - ZIELONE_POLE - RAMKA;

    pozycjaStartu = Math.max(0, Math.min(x, SZEROKOSC_TABELI));

    ustawGornaStrzalke();
}

function pozycjaWiersza(path) {
    return (path - 1) * WIERSZ + (WIERSZ - WYSOKOSC_ZADANIA) / 2;
}

function addTask() {
    let nazwa = $$("taskInput").value.trim();
    let dni = Number($$("days").value);
    let path = Number($$("path").value);
    let table = $$("table");


    licznikZadan++;

    let zadanie = document.createElement("div");
    let szerokosc = dni * KOLUMNA;
    let maxLeft = Math.max(0, SZEROKOSC_TABELI - szerokosc);
    let left = Math.min(pozycjaStartu, maxLeft);

    zadanie.className = "zadanie";
    zadanie.id = "zadanie_" + licznikZadan;
    zadanie.textContent = nazwa;

    zadanie.dataset.nazwa = nazwa;
    zadanie.dataset.start = Math.round(left);
    zadanie.dataset.dni = dni;
    zadanie.dataset.path = path;

    zadanie.style.left = left + "px";
    zadanie.style.top = pozycjaWiersza(path) + "px";
    zadanie.style.width = Math.max(20, szerokosc - 4) + "px";

    table.appendChild(zadanie);
    $$("taskInput").value = "";

    const start_day = Math.floor(left / KOLUMNA) + 1;

    zadanie.addEventListener("click", () => {
        alert(`Trwa: ${dni} dni\n Rozpoczęcie: ${start_day} dnia`)
    });
}


function stworzPanelOsob() {
    let panel = $$("people");
    let user = $$("user").value;

    for (let path = 1; path <= LICZBA_SCIEZEK; path++) {
        osobyNaSciezkach[path] = [];


        let wiersz = document.createElement("div");
        wiersz.className = "wierszOsob";
        wiersz.id = "wierszOsob_" + path;
   

        panel.appendChild(wiersz);
    }
}

function przypiszOsobe() {
    let lista = $$("userList");
    let path = Number($$("path").value);

    let imie = lista.options[lista.selectedIndex].text;


    osobyNaSciezkach[path].push(imie);

    odswiezPanelOsob();
}



function odswiezPanelOsob() {
    let lista = $$("userList");
    let wybranaOsoba = lista.selectedIndex === -1 ? "" : lista.options[lista.selectedIndex].text;



    for (let path = 1; path <= LICZBA_SCIEZEK; path++) {
        let wiersz = $$("wierszOsob_" + path);
        wiersz.innerHTML = "";



        osobyNaSciezkach[path].forEach(function (imie) {
            let kropka = document.createElement("div");

            kropka.className = "kropka";
            kropka.title = imie;

            kropka.addEventListener("mouseover", () => {
                kropka.classList.add("kropkaWybrana");
             });
             kropka.addEventListener("mouseout", () => {
                kropka.classList.remove("kropkaWybrana");
             });



           

            wiersz.appendChild(kropka);
        });
    }
}




$$("addTaskUser").addEventListener("click", przypiszOsobe);
$$("userList").addEventListener("change", odswiezPanelOsob);
$$("addTaskButton").addEventListener("click", addTask);



createTableNet();
stworzPanelOsob();
odswiezPanelOsob();
arrowMove();
ustawGornaStrzalke();

