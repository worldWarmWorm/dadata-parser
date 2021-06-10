<?php
    require_once './helpers/CSV.php';

    $csv = new CSV("data.csv", "newdata.csv");
    $addressesArr = [];
    foreach ($csv->readCSV() as $k => $value) {
        $addressesArr[$k + 1] =
            (($value[1] == "(пусто)") ? null : $value[1]) . ' ' .
            (($value[2] == "(пусто)") ? null : $value[2]) . ' ' .
            (($value[3] == "(пусто)") ? null : stristr($value[3], ',', true));
    }
//    $csv->prettifyArray($addressesArr);
    $jsonAddresses = json_encode($addressesArr);

//    $arr = ["02322722-58e6-4b7c-9203-ce1a79b1910e","c4b8e033-1dab-42b1-836a-0a916168dd70","552054e3-09d0-44fa-894c-34997e757101","afdba8ea-981a-4861-a7c1-d48dc843d401",null];
//
//    foreach ($arr as $k => $v) {
//        $arr_push[$k + 1] = $v;
//    }
//    $csv->prettifyArray($arr_push);
//    $csv->writeCSV($arr_push);
?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title>Dadata</title>
    </head>
    <body>
        <h1>/dadata/fias-id-parser</h1>
        <br>
        <p>Open console for watching program's work</p>
        <button type="button" id="start">Start parsing</button>
        <button type="button" id="result">Show result of parsing</button>
        <button type="button" id="storage">Get localStorage</button>

        <br>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                let preparedData = {
                        catchedAddressesArr: Object.values(<?= $jsonAddresses ?>),
                        getQueryes: function() {
                            let addresses = [];
                            for(let i = 0; i <= this.catchedAddressesArr.length; i++) {
                                addresses[i] = this.catchedAddressesArr[i];
                                if(i >= 9300) break;
                            }
                            return addresses;
                        }
                    },
                    queryes = preparedData.getQueryes(),
                    btn = document.getElementById('start'),
                    btn2 = document.getElementById('result'),
                    btn3 = document.getElementById('storage'),
                    fias_id_arr = [],
                    accepted = [],
                    rejected = [];

                btn.addEventListener('click', () => {
                    function fecthData(queryNum = 0) {
                        setTimeout( function launch() {
                            let entryPoints = {
                                    url: "https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address",
                                    token: "d04f85b97ddfe613eb894481695fa9a401447df0",
                                    query: queryes[queryNum]
                                },
                                options = {
                                    method: "POST",
                                    mode: "cors",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "Accept": "application/json",
                                        "Authorization": `Token ${entryPoints.token}`
                                    },
                                    body: JSON.stringify({query: entryPoints.query})
                                }

                            async function getResponses() {
                                await fetch(entryPoints.url, options)
                                        .then(response => response.text())
                                        .then(result => (function () {
                                            let suggestions = JSON.parse(result).suggestions,
                                                    fiasId = suggestions[0].data.fias_id,
                                                    address = suggestions[0].value;

                                            fias_id_arr[queryNum] = fiasId;
                                            // console.log(`Запросов обработано: ${queryNum} из ${queryes.length} \n`);
                                            accepted.push(queryNum);
                                            console.log(queryNum + ' -- ' + address + ' -- ' + fiasId)
                                        }()))
                                        .catch((error) => {
                                            rejected.push(queryNum);
                                            console.log("error", error);
                                        })
                            }
                            getResponses();

                            if(queryNum < queryes.length) {
                                setTimeout(launch, 1100)
                                queryNum++;
                            }
                        }, 1100)
                    }
                    fecthData();

                    setTimeout(function saveInStorage() {
                        localStorage.setItem("data", JSON.stringify(fias_id_arr));
                        localStorage.setItem("saved", accepted.length);
                        localStorage.setItem("lost", rejected.length);
                        setTimeout(saveInStorage, 900000);
                    }, 900000);
                });

                btn2.addEventListener('click', () => {
                    console.log(JSON.stringify(fias_id_arr));
                    console.log(accepted);
                    console.log(rejected);
                });

                btn3.addEventListener('click', () => {
                    if (localStorage.getItem('data') && localStorage.getItem('saved') && localStorage.getItem('lost')) {
                        console.log(localStorage.getItem('data'));
                        console.log(localStorage.getItem('saved'));
                        console.log(localStorage.getItem('lost'));
                    } else {
                        console.log('В localStorage пусто');
                    }
                });
            })
        </script>
    </body>
</html>