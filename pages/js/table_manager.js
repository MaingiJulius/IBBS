document.addEventListener('DOMContentLoaded', function () {
    const table = document.querySelector('.crud-table');
    const cardContainer = document.querySelector('.ticket-container');
    if (!table && !cardContainer) return;
    const searchHub = document.createElement('div');
    searchHub.id = 'search-hub';
    searchHub.className = 'search-hub-animation';
    searchHub.style = `
        display: flex; gap: 12px; margin: 20px 0; 
        justify-content: flex-end; align-items: center; 
        background: #f8fafc; padding: 12px 20px; border-radius: 15px;
        border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        flex-wrap: wrap;
    `;
    let categories = [];
    if (table) {
        categories = Array.from(table.rows[0].cells).map((cell, index) => ({
            label: cell.innerText.replace('⇅', '').trim(),
            index: index,
            type: 'table'
        })).filter(cat => cat.label && cat.label !== 'Action' && cat.label !== 'Control Deck');
    } else {
        categories = [
            { label: 'All Fields', index: -1, type: 'card' },
            { label: 'Destination', index: 'h3', type: 'card' },
            { label: 'Seat', index: 'seat', type: 'card' },
            { label: 'Traveler', index: 'traveler', type: 'card' }
        ];
    }
    let catSelectHtml = `<select id="sort-category" style="padding: 10px; border-radius: 10px; border: 1px solid #cbd5e1; outline:none; font-size: 0.9rem; cursor:pointer; background: white;">
        <option value="-1">All Categories</option>`;
    categories.forEach(cat => {
        if (cat.index !== -1) catSelectHtml += `<option value="${cat.index}">${cat.label}</option>`;
    });
    catSelectHtml += `</select>`;
    searchHub.innerHTML = `
        <div style="display:flex; align-items:center; gap:8px;">
            <span style="font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Filter By:</span>
            ${catSelectHtml}
        </div>
        <div style="position:relative; flex-grow: 1; max-width: 300px;">
            <input type="text" id="global-search" list="search-options" placeholder="Type to search..." style="width:100%; padding: 10px 15px; border-radius: 10px; border: 2px solid #cbd5e1; outline:none; font-size: 0.9rem; transition: border-color 0.2s;">
            <datalist id="search-options"></datalist>
        </div>
        <div style="display:flex; gap:8px;">
            <button id="search-btn" class="button regular-button pink-background" style="margin:0; width: auto; padding: 10px 25px; font-size: 0.9rem;">Search</button>
            <button id="reset-btn" class="button regular-button" style="margin:0; width: auto; padding: 10px 20px; font-size: 0.9rem; background: #f1f5f9; color:#64748b; box-shadow:none; border: 1px solid #e2e8f0;">Reset</button>
        </div>
    `;
    if (table) table.parentNode.insertBefore(searchHub, table);
    else cardContainer.parentNode.insertBefore(searchHub, cardContainer);
    const searchInput = document.getElementById('global-search');
    const dataList = document.getElementById('search-options');
    const catSelect = document.getElementById('sort-category');
    const searchBtn = document.getElementById('search-btn');
    const resetBtn = document.getElementById('reset-btn');
    function populateOptions() {
        dataList.innerHTML = '';
        const index = catSelect.value;
        const seen = new Set();
        const cleanValue = (val) => {
            if (!val) return "";
            return val.split(/ to | → |,/).shift().trim();
        };
        if (table) {
            const rows = Array.from(table.rows).slice(1);
            rows.forEach(row => {
                const cells = row.cells;
                if (index === "-1") {
                    Array.from(cells).forEach(c => seen.add(cleanValue(c.innerText.trim())));
                } else if (cells[index]) {
                    seen.add(cleanValue(cells[index].innerText.trim()));
                }
            });
        } else {
            const cards = document.querySelectorAll('.ticket-card');
            cards.forEach(card => {
                if (index === "-1") {
                    seen.add(cleanValue(card.innerText.trim()));
                } else if (index === "h3") {
                    seen.add(cleanValue(card.querySelector('h3').innerText.trim()));
                } else {
                    seen.add(cleanValue(card.innerText.trim()));
                }
            });
        }
        Array.from(seen).sort().slice(0, 20).forEach(val => {
            if (!val) return;
            const opt = document.createElement('option');
            opt.value = val;
            dataList.appendChild(opt);
        });
    }
    catSelect.onchange = populateOptions;
    populateOptions();
    function performSearch() {
        const filter = searchInput.value.toLowerCase().trim();
        const catIdx = catSelect.value;
        if (table) {
            const rows = table.getElementsByTagName('tr');
            for (let i = 1; i < rows.length; i++) {
                let visible = false;
                const cells = rows[i].getElementsByTagName('td');
                if (catIdx === "-1") {
                    for (let j = 0; j < cells.length; j++) {
                        if (cells[j].innerText.toLowerCase().includes(filter)) { visible = true; break; }
                    }
                } else if (cells[catIdx]) {
                    if (cells[catIdx].innerText.toLowerCase().includes(filter)) visible = true;
                }
                rows[i].style.display = visible ? '' : 'none';
            }
        } else {
            const cards = document.querySelectorAll('.ticket-card');
            cards.forEach(card => {
                let visible = false;
                if (catIdx === "-1") {
                    if (card.innerText.toLowerCase().includes(filter)) visible = true;
                } else if (catIdx === "h3") {
                    if (card.querySelector('h3').innerText.toLowerCase().includes(filter)) visible = true;
                } else {
                    if (card.innerText.toLowerCase().includes(filter)) visible = true;
                }
                card.style.display = visible ? 'flex' : 'none';
            });
        }
    }
    searchBtn.onclick = performSearch;
    searchInput.onkeyup = (e) => { if (e.key === 'Enter') performSearch(); };
    resetBtn.onclick = () => {
        searchInput.value = '';
        catSelect.value = '-1';
        populateOptions();
        performSearch();
    };
    if (table) {
        const headers = table.querySelectorAll('th');
        headers.forEach((header, index) => {
            if (header.innerText.trim() === 'Action' || header.innerText.trim() === 'Control Deck') return;
            header.style.cursor = 'pointer';
            header.innerHTML += ' <span style="font-size: 0.6em; opacity: 0.5;">⇅</span>';
            header.addEventListener('click', () => sortTable(index));
        });
        let sortOrder = 1;
        function sortTable(index) {
            const rows = Array.from(table.rows).slice(1);
            const sortedRows = rows.sort((a, b) => {
                let valA = a.cells[index].innerText.replace('⇅', '').trim();
                let valB = b.cells[index].innerText.replace('⇅', '').trim();
                if (valA.includes(',') && valB.includes(',')) {
                    valA = valA.split(',').pop().trim();
                    valB = valB.split(',').pop().trim();
                }
                const numA = parseFloat(valA.replace(/[^0-9.-]+/g, ""));
                const numB = parseFloat(valB.replace(/[^0-9.-]+/g, ""));
                if (!isNaN(numA) && !isNaN(numB)) return (numA - numB) * sortOrder;
                return valA.localeCompare(valB) * sortOrder;
            });
            sortOrder *= -1;
            sortedRows.forEach(row => table.tBodies[0].appendChild(row));
        }
    }
});