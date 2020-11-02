<?php
include('private/components/category.php');
?>
<div class="flex-row flex-column-phone">
    <div class="col-25">
        <div class="container">
            <h2>Filters</h2>
            <ul class="filters no-bullet no-select container" id="category-root">
                <h3>Category</h3>
                <?php 
                    $stmt = prepareStmt("SELECT name FROM categories WHERE parent IS NULL ORDER BY name");
                    $result = executeStmt($stmt);
                    while($row = $result->fetch_assoc()) {
                        comp_categoryFolder($row['name']);
                    }
                ?>
            </ul>
            <div class="container">
                <h3>Manufacturer</h3>
                <ul id="manufacturers" class="filters no-bullet no-select"></ul>
            </div>
            <div class="container">
                <h3>Max Price</h3>
                <div class="flex-row flex-space">
                    <div id="price-min" class="text-tiny">0</div>
                    <div id="price-label">100000</div>
                    <div id="price-max" class="text-tiny">100000</div>
                </div>
                <input name="price-max" id="price-slider" class="col-100" type="range" min="0" max="100000" value="100000" onchange="priceSliderUpdated(this)"></input>
            </div>
            <input type="text" id="filter-search" placeholder="search" value="<?php
                if(isset($_POST["query"])) {
                    echo $_POST["query"];
                }
            ?>">
            <button class="btn" onclick="firstPage(true)">Apply filters</button>
        </div>
    </div>
    <div class="col-75">
        <div class="container">
            <div id="shop-grid" class="grid grid-4">
            </div>
            <div class="flex-row flex-align-center flex-center">
                <button class="btn" onclick="firstPage(false)">First</button>
                <button class="btn" onclick="prevPage()">Previous</button>
                <span>
                    <input type="text" id="browse-page" value="1" onchange="loadPage()" />
                    / <span id="page-count">100</span>
                </span>
                <button class="btn" onclick="nextPage()">Next</button>
                <button class="btn" onclick="lastPage()">Last</button>
            </div>
        </div>
    </div>
</div>
<script src="/js/browse.js"></script>
