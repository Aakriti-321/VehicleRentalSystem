let price = 0;
const today = new Date().toISOString().split('T')[0];

function openForm(id, perDayPrice) {
    const form = document.getElementById('bookingForm');
    form.style.display = "flex";

    price = perDayPrice;
    document.getElementById('vehicle_id').value = id;

    const start = document.getElementById('start_date');
    const end = document.getElementById('end_date');

    
    start.value = "";
    end.value = "";
    start.min = today;
    end.min = today;

    document.getElementById('num_days_display').textContent = "0";
    document.getElementById('total_price').textContent = "0";
}

function closeForm() {
    document.getElementById('bookingForm').style.display = "none";
}

function calculateTotal() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    if (!startDate || !endDate) return;

    
    if (new Date(endDate) < new Date(startDate)) {
        alert("End date cannot be before start date");
        document.getElementById('end_date').value = "";
        return;
    }

    const days = (new Date(endDate) - new Date(startDate)) / (1000 * 60 * 60 * 24) + 1;
    const total = days * price;

    document.getElementById('num_days_display').textContent = days;
    document.getElementById('total_price').textContent = total;
    document.getElementById('num_days_input').value = days;
    document.getElementById('total_price_input').value = total;

    
    document.getElementById('end_date').min = startDate;
}


document.getElementById("searchInput").addEventListener('input', function() {
    const text = this.value.toLowerCase();
    document.querySelectorAll(".vehicle-card").forEach(card => {
        card.style.display = card.innerText.toLowerCase().includes(text) ? "block" : "none";
    });
});