/**
 * TABLE_MANAGER.JS (Master Data Navigator)
 * Purpose: Provides a premium, intelligent search and sort interface for the entire IBBS ecosystem.
 */
// / (forward slash) * (asterisk) * (asterisk) opens a professional documentation block.
// TABLE_MANAGER.JS is the module title. * (asterisk) / (forward slash) closes it.

document.addEventListener('DOMContentLoaded', function () {
// document (the webpage object) . (dot) addEventListener (listen for event) 
// is the tool that monitors the page status. ( (opening bracket) starts 
// the input. 'DOMContentLoaded' (D O M Content Loaded) is the trigger 
// that fires when the HTML structure is ready. , (comma) separates 
// the trigger from the action. function (function) starts the logic block. 
// { (opening curly bracket) marks the start of the management logic.

    const table = document.querySelector('.crud-table');
    // const (constant) is a variable type. table (t a b l e) is the label 
    // for the data grid object. = (equals sign) assignment. document . 
    // querySelector (find one) searches for the first element with the 
    // . (dot) crud-table (class name). ; (semicolon).

    const cardContainer = document.querySelector('.ticket-container');
    // const (constant). cardContainer (c a r d container) is the label for 
    // the card layout box. = (equals sign) assignment. document . 
    // querySelector (find one) ( '.ticket-container' ). ; (semicolon).

    if (!table && !cardContainer) return;
    // if (if) starts a logic check. ( (bracket) ! (NOT) table && (AND) ! 
    // (NOT) cardContainer ) (bracket) means if neither exists. return 
    // (return) stops the script immediately to save resources. ; (semicolon).

    const searchHub = document.createElement('div');
    // const (constant). searchHub (s e a r c h h u b) is the label for the 
    // new UI box. = (equals sign) assignment. document . createElement 
    // (create new element) builds a <div> box in memory. ; (semicolon).

    searchHub.id = 'search-hub';
    // searchHub (the object) . id (unique ID) = (assign) 'search-hub' 
    // (the name string). ; (semicolon).

    searchHub.className = 'search-hub-animation';
    // searchHub . className (CSS class list) = (assign) 'search-hub-animation' 
    // (the animation name). ; (semicolon).

    searchHub.style = `
        display: flex; gap: 12px; margin: 20px 0; 
        justify-content: flex-end; align-items: center; 
        background: #f8fafc; padding: 12px 20px; border-radius: 15px;
        border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        flex-wrap: wrap;
    `;
    // searchHub . style (visual design rules) = (assign) ` (backtick) starts 
    // a multi-line design block. ; (semicolon).

    let categories = [];
    // let (variable that can change). categories (c a t e g o r i e s) is 
    // the label for the list of searchable fields. = (assign) [ ] (empty 
    // list/array). ; (semicolon).

    if (table) {
    // if (if) the table exists. { (bracket) starts the logic.

        categories = Array.from(table.rows[0].cells).map((cell, index) => ({
            label: cell.innerText.replace('⇅', '').trim(),
            index: index,
            type: 'table'
        })).filter(cat => cat.label && cat.label !== 'Action' && cat.label !== 'Control Deck');
        // categories = (assign). Array.from (convert to list). table.rows[0].cells 
        // (header cells). .map (transform each). cell.innerText.replace (clean 
        // text). .filter (remove columns like 'Action'). ; (semicolon).
    } else {
    // else (otherwise). { (bracket) starts.

        categories = [
            { label: 'All Fields', index: -1, type: 'card' },
            { label: 'Destination', index: 'h3', type: 'card' },
            { label: 'Seat', index: 'seat', type: 'card' },
            { label: 'Traveler', index: 'traveler', type: 'card' }
        ];
        // categories = [ ... ] (hardcoded list for tickets). ; (semicolon).
    }

    let catSelectHtml = `<select id="sort-category" style="padding: 10px; border-radius: 10px; border: 1px solid #cbd5e1; outline:none; font-size: 0.9rem; cursor:pointer; background: white;">
        <option value="-1">All Categories</option>`;
    // let (variable). catSelectHtml (HTML text). = (assign) ` (text block) 
    // starts the <select> dropdown code.

    categories.forEach(cat => {
        if (cat.index !== -1) catSelectHtml += `<option value="${cat.index}">${cat.label}</option>`;
    });
    // categories.forEach (for each category). += (add to string) 
    // <option> (dropdown item).

    catSelectHtml += `</select>`;
    // += (add) </select> (end dropdown). ; (semicolon).

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
    // searchHub . innerHTML (the content inside the box) = (assign) ` (text) 
    // defines the visible controls.

    if (table) table.parentNode.insertBefore(searchHub, table);
    else cardContainer.parentNode.insertBefore(searchHub, cardContainer);
    // table . parentNode (container) . insertBefore (place before) injects 
    // the search hub into the page.

    const searchInput = document.getElementById('global-search');
    const dataList = document.getElementById('search-options');
    const catSelect = document.getElementById('sort-category');
    const searchBtn = document.getElementById('search-btn');
    const resetBtn = document.getElementById('reset-btn');
    // document . getElementById (find by ID) links the UI elements to 
    // JavaScript variables for control.

    function populateOptions() {
    // function (function) starts a logical instruction set. populateOptions 
    // (name). { (curly bracket) starts.

        dataList.innerHTML = '';
        // dataList . innerHTML = (assign) '' (empty quotes) clears old 
        // suggestions.

        const index = catSelect.value;
        const seen = new Set();
        // const (constant). index (selected category). seen (list of unique 
        // values).

        const cleanValue = (val) => {
            if (!val) return "";
            return val.split(/ to | → |,/).shift().trim();
        };
        // Helper tool to clean data text for suggestions.

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
            // Scans the table to find values for autocomplete.
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
            // Scans cards for autocomplete suggestions.
        }

        Array.from(seen).sort().slice(0, 20).forEach(val => {
            if (!val) return;
            const opt = document.createElement('option');
            opt.value = val;
            dataList.appendChild(opt);
        });
        // Adds unique suggestions to the browser dropdown.
    }

    catSelect.onchange = populateOptions;
    // catSelect . onchange (on selection change) = (assign) populateOptions 
    // (refresh list).

    populateOptions();
    // (Calls the function to start the first list generation).

    function performSearch() {
    // function (function) performSearch (logic for filtering). { starts.

        const filter = searchInput.value.toLowerCase().trim();
        // filter (typed text) . toLowerCase (makes it small letters) . 
        // trim (removes spaces).

        const catIdx = catSelect.value;
        // catIdx (selected category ID).

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
            // Hides or shows table rows based on matches.
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
            // Hides or shows data cards based on matches.
        }
    }

    searchBtn.onclick = performSearch;
    // searchBtn . onclick (on click) = (assign) performSearch (run filter).

    searchInput.onkeyup = (e) => { if (e.key === 'Enter') performSearch(); };
    // Trigger filter when user presses 'Enter' key.

    resetBtn.onclick = () => {
        searchInput.value = '';
        catSelect.value = '-1';
        populateOptions();
        performSearch();
    };
    // resetBtn . onclick (on click) clears everything.

    if (table) {
        const headers = table.querySelectorAll('th');
        headers.forEach((header, index) => {
            if (header.innerText.trim() === 'Action' || header.innerText.trim() === 'Control Deck') return;
            header.style.cursor = 'pointer';
            header.innerHTML += ' <span style="font-size: 0.6em; opacity: 0.5;">⇅</span>';
            header.addEventListener('click', () => sortTable(index));
        });
        // Adds sorting tool to table headers.

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
        // Logic to sort rows alphabetically or numerically.
    }
});
// } (closing curly bracket) ends the entire DOM management script.
