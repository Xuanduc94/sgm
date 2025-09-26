function callAPI(controller, action) {
    return `${location.origin}?c=${controller}&a=${action}`
}
const client = new HttpClient({
    baseURL: location.origin,
    timeout: 8000,
    retries: 2,
});
DataTable.ext.search.push(function (settings, data) {
    let min = document.getElementById('minDate').value;
    let max = document.getElementById('maxDate').value;
    const dateStr = data[2];
    min = new Date(min);
    max = new Date(max);
    const date = new Date(formatDdMmYyyyToYyyyMmDd(dateStr));
    if (min.getTime() < date.getTime() && date.getTime() < max.getTime()) {
        return true;
    }
    return false;
});
function formatDdMmYyyyToYyyyMmDd(dateString) {
    const parts = dateString.split('/'); // Splits "dd/mm/yyyy" into ["dd", "mm", "yyyy"]
    if (parts.length === 3) {
        const day = parts[0];
        const month = parts[1];
        const year = parts[2];
        return new Date(`${year}-${month}-${day}`); // Reassembles as "yyyy-mm-dd"
    } else {
        return "Invalid date format"; // Handle cases where input format is incorrect
    }
}
let table = new DataTable('table#construction', {
    responsive: true,
    processing: true,
    serverSide: false,
    ajax: { url: callAPI('Contrucstion', 'getAll'), dataSrc: "" },
    columns: [
        { data: 'WBS' },
        { data: 'FunctionCode' },
        { data: 'Date' },
        { data: 'User' },
        {
            data: 'Status', render: function (data, type, row) {
                return getStatus(data);
            }
        }
    ]
});
document.getElementById('minDate').addEventListener('change', () => table.draw());
document.getElementById('maxDate').addEventListener('change', () => table.draw());
function getStatus(status) {
    let v = { 0: "Từ chối", 1: "Trình ký", 2: "Ban hành" }
    switch (status) {
        case 0:
            return `<span style="color: red">${v[status]}</span>`
        case 1:
            return `<span style="color: green">${v[status]}</span>`
        case 1:
            return `<span style="color: blue">${v[status]}</span>`
        default:
            return `<span style="color: green">${v[status]}</span>`
    }
}

async function submitForm() {
    let data = new FormData();
    data.append("file", $('#file-excel').prop('files')[0]);
    let res = await client.post(callAPI('Contrucstion', 'insert'), data, { headers: "contentType: multipart/form-data" })
    if (res.data == "OK") {
        location.reload();
    }
}

async function loadData() {
    let res = await client.get(callAPI('Contrucstion', 'getAll'))
    console.log(res.data);

}