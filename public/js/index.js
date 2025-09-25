function callAPI(controller, action) {
    return `${location.origin}?c=${controller}&a=${action}`
}
const client = new HttpClient({
    baseURL: location.origin,
    timeout: 8000,
    retries: 2,
});


let grid = $("div#construction").Grid({
    columns: ['Tên công trình', 'Mã trạm', 'Ngày ký', "Trạng thái", "Người trình"],
    pagination: true, search: true, sort: true,
    server: {
        url: callAPI('Contrucstion', 'getAll'),
        then: data => data.data.map(card => [card.WBS, card.FunctionCode, card.Date, card.Status, card.User])
    }
});

$(document).ready(function () {
    // loadData();
})

function submitForm() {
    $('#formImport').submit();
}

async function loadData() {
    let res = await client.get(callAPI('Contrucstion', 'getAll'))
    console.log(res.data);

}