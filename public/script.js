const submit_btn = document.getElementById("submit");
const data_table = document.getElementById("data");
const select = document.getElementById('user');

submit_btn.onclick = function (e) {
  e.preventDefault();
  data_table.style.display = "block";
  fetch(`http://localhost:80?user=${select.value}`, {method: "GET"})
    .then(response => response.json())
    .then(data => {
      let tBody = '<tr><th>Mounth</th><th>Amount</th><th>Count</th></tr>';
      data.forEach(balancePerMonth => {
        tBody += `<tr><th>${balancePerMonth.month}</th><th>${balancePerMonth.balance}</th><th>${balancePerMonth.count}</th></tr>`;
      })
      data_table.querySelector('table').innerHTML = tBody;
    })
};
