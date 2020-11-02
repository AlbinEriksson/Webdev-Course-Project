<?php
function comp_categoryLabel($categoryName, $hasChildren) { ?>
    <div class="category-label pointer" onclick="toggleCategorySelected(this)"> <?php
        if($hasChildren) { ?>
            <div class="folder-icon square-image-frame"></div> <?php
        } ?>
        <span class="category-name"><?=$categoryName?></span>
    </div> <?php
}

function comp_categoryFolder($categoryName) {
    $children = getChildrenOfCategory($categoryName);
    $hasChildren = $children->num_rows > 0; ?>

    <li class="category-item"> <?php
        comp_categoryLabel($categoryName, $hasChildren);
        if($hasChildren) { ?>
            <ul class="categories no-bullet"> <?php
                while($childCategory = $children->fetch_array(MYSQLI_NUM)) {
                    comp_categoryFolder($childCategory[0]);
                }
                ?>
            </ul>
        <?php } ?>
    </li>
<?php
}
?>
