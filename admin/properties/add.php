<?php
// 1. DATABASE & LOGIC FIRST (No HTML output before header redirect)
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/blueprint-helpers.php';

ensureBlueprintTable($conn);

$edit = false;
$property = [];
$selectedAmenities = [];
$propertyImages = [];
$blueprint = null;
$id = 0;

// Load Data if Editing
if (isset($_GET['id']) && intval($_GET['id']) > 0) {
    $edit = true;
    $id = intval($_GET['id']);
    $property = $conn->query("SELECT * FROM properties WHERE id=$id")->fetch_assoc();
    
    $aRes = $conn->query("SELECT amenity_id FROM property_amenities WHERE property_id=$id");
    while ($r = $aRes->fetch_assoc()) { $selectedAmenities[] = $r['amenity_id']; }

    $imgRes = $conn->query("SELECT image_path FROM property_images WHERE property_id=$id");
    while ($r = $imgRes->fetch_assoc()) { $propertyImages[] = $r['image_path']; }

    $blueprint = getBlueprint($conn, $id);
}

// 2. MAIN SAVE LOGIC
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    
    if (isset($_POST['property_id']) && intval($_POST['property_id']) > 0) {
        $id = intval($_POST['property_id']);
        $edit = true; 
    }

    $name        = trim($_POST['name']);
    $type        = trim($_POST['type']);
    $location    = trim($_POST['location']);
    $size        = trim($_POST['size']);
    $dimensions  = trim($_POST['dimensions']);
    $facing      = trim($_POST['facing']);
    $status      = trim($_POST['status']);
    $description = trim($_POST['description']);
    $selected    = $_POST['amenities'] ?? [];
    $blueprintData = $_POST['blueprint_data'] ?? '';

    if ($edit) {
        $stmt = $conn->prepare("UPDATE properties SET name=?, type=?, location=?, size=?, dimensions=?, facing=?, status=?, description=? WHERE id=?");
        $stmt->bind_param("ssssssssi", $name, $type, $location, $size, $dimensions, $facing, $status, $description, $id);
        $stmt->execute();
        $stmt->close();
        $conn->query("DELETE FROM property_amenities WHERE property_id=$id");
    } else {
        $stmt = $conn->prepare("INSERT INTO properties (name,type,location,size,dimensions,facing,status,description) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssssss", $name, $type, $location, $size, $dimensions, $facing, $status, $description);
        $stmt->execute();
        $id = $stmt->insert_id; 
        $stmt->close();
    }

    // Amenities
    if (!empty($selected)) {
        $stmt = $conn->prepare("INSERT INTO property_amenities (property_id, amenity_id) VALUES (?,?)");
        foreach ($selected as $aid) {
            $aid = intval($aid);
            $stmt->bind_param("ii", $id, $aid);
            $stmt->execute();
        }
        $stmt->close();
    }

    // Upload Handler (Images)
    $imgDir = __DIR__ . '/../uploads';
    if (!is_dir($imgDir)) mkdir($imgDir, 0777, true);
    if (!empty($_FILES['images']['name'][0])) {
        $stmt = $conn->prepare("INSERT INTO property_images (property_id, image_path) VALUES (?,?)");
        foreach ($_FILES['images']['name'] as $i => $file) {
            $tmp = $_FILES['images']['tmp_name'][$i];
            if ($tmp) {
                $clean = time().'_'.preg_replace('/[^A-Za-z0-9.\-_]/','_',$file);
                if (move_uploaded_file($tmp, "$imgDir/$clean")) {
                    $stmt->bind_param("is", $id, $clean);
                    $stmt->execute();
                }
            }
        }
        $stmt->close();
    }

    // Document Handler
    $docDir = __DIR__ . '/../uploads/documents';
    if (!is_dir($docDir)) mkdir($docDir, 0777, true);
    if (!empty($_FILES['documents']['name'][0])) {
        $stmt = $conn->prepare("INSERT INTO property_documents (property_id, document_title, document_path) VALUES (?,?,?)");
        foreach ($_FILES['documents']['name'] as $i => $file) {
            $tmp = $_FILES['documents']['tmp_name'][$i];
            if ($tmp && strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'pdf') {
                $clean = time().'_'.preg_replace('/[^A-Za-z0-9.\-_]/','_',$file);
                if (move_uploaded_file($tmp, "$docDir/$clean")) {
                    $title = pathinfo($file, PATHINFO_FILENAME);
                    $stmt->bind_param("iss", $id, $title, $clean);
                    $stmt->execute();
                }
            }
        }
        $stmt->close();
    }

    // Blueprint Handler
    $bpDir = __DIR__ . '/../uploads/blueprints';
    if (!is_dir($bpDir)) mkdir($bpDir, 0777, true);
    $prev = getBlueprint($conn, $id);
    $bpOriginal = $prev['original_path'] ?? null;
    $bpAnnotated = $prev['annotated_path'] ?? null;

    if (!empty($_FILES['blueprint_image']['tmp_name'])) {
        $clean = time().'_'.preg_replace('/[^A-Za-z0-9.\-_]/','_',$_FILES['blueprint_image']['name']);
        if (move_uploaded_file($_FILES['blueprint_image']['tmp_name'], "$bpDir/$clean")) $bpOriginal = $clean;
    }
    if (!empty($blueprintData)) {
        $parts = explode(',', $blueprintData, 2);
        $decoded = base64_decode($parts[1] ?? '');
        if ($decoded) {
            $annot = 'bp_'.time().'_'.rand(1000,9999).'.png';
            file_put_contents("$bpDir/$annot", $decoded);
            $bpAnnotated = $annot;
        }
    }
    if ($bpOriginal || $bpAnnotated) {
        $stmt = $conn->prepare("INSERT INTO property_blueprints (property_id, original_path, annotated_path) VALUES (?,?,?) ON DUPLICATE KEY UPDATE original_path=VALUES(original_path), annotated_path=VALUES(annotated_path)");
        $stmt->bind_param("iss", $id, $bpOriginal, $bpAnnotated);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: list.php");
    exit;
}

// Load UI Helpers
$amenities = [];
$resA = $conn->query("SELECT * FROM amenities ORDER BY name ASC");
while ($row = $resA->fetch_assoc()) { $amenities[] = $row; }

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';

$isLocked = ($edit && !empty($blueprint['annotated_path']));
?>

<style>
    /* Blueprint Toolbar Styling */
    .bp-toolbar {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        margin-bottom: 15px;
        width: fit-content;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .bp-tool-btn {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid transparent;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.2s;
        color: #475569;
    }
    .bp-tool-btn:hover { background: #e2e8f0; color: #0f172a; }
    .bp-tool-btn.active { background: #3b82f6; color: #fff; border-color: #2563eb; }
    .bp-tool-btn:disabled { opacity: 0.4; cursor: not-allowed; }
    
    .bp-divider { width: 1px; height: 24px; background: #e2e8f0; margin: 0 4px; }

    .color-picker-box {
        padding: 6px 10px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 14px;
        outline: none;
        cursor: pointer;
        background: #fff;
    }

    .blueprint-canvas-wrap {
        background: #f1f5f9;
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
        display: flex;
        justify-content: center;
        min-height: 400px;
    }
    #blueprintCanvas { background: #fff; cursor: crosshair; }
    .editor-locked-ui { opacity: 0.5; pointer-events: none; }

    /* Amenities UI Improvement */
    .amenities-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 12px;
        margin-top: 10px;
    }
    .amenity-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .amenity-item:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }
    .amenity-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    .amenity-item span {
        font-size: 14px;
        color: #334155;
        font-weight: 500;
    }
</style>

<div class="admin-layout">
  <main class="admin-main admin-main--wide">
    <div class="form-page">
      <header class="form-header">
        <h1><?= $edit ? "Edit Property" : "Add Property"; ?></h1>
        <p><?= $edit ? "Make changes and click Save Property" : "Create a new property listing"; ?></p>
      </header>

      <form class="property-form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="property_id" value="<?= $id; ?>">

        <section class="form-card">
          <h3>Basic Information</h3>
          <div class="form-grid">
            <div class="col-6"><label>Property Name *</label><input type="text" name="name" required value="<?= $edit ? htmlspecialchars($property['name']) : '' ?>"></div>
            <div class="col-6">
              <label>Property Type *</label>
              <select name="type" required>
                <option value="">Select Type</option>
                <option <?= $edit && $property['type']=="Plot" ? "selected" : "" ?>>Plot</option>
                <option <?= $edit && $property['type']=="Villa" ? "selected" : "" ?>>Villa</option>
                <option <?= $edit && $property['type']=="Home Stays" ? "selected" : "" ?>>Home Stays</option>
              </select>
            </div>
            <div class="col-6"><label>Location *</label><input type="text" name="location" required value="<?= $edit ? htmlspecialchars($property['location']) : '' ?>"></div>
            <div class="col-6"><label>Size *</label><input type="text" name="size" required value="<?= $edit ? htmlspecialchars($property['size']) : '' ?>"></div>
            <div class="col-6"><label>Dimensions *</label><input type="text" name="dimensions" required value="<?= $edit ? htmlspecialchars($property['dimensions']) : '' ?>"></div>
            <div class="col-6"><label>Facing *</label><input type="text" name="facing" required value="<?= $edit ? htmlspecialchars($property['facing']) : '' ?>"></div>
            <div class="col-6">
              <label>Status *</label>
              <select name="status">
                <option <?= $edit && $property['status']=="Available" ? "selected" : "" ?>>Available</option>
                <option <?= $edit && $property['status']=="Sold" ? "selected" : "" ?>>Sold</option>
                <option <?= $edit && $property['status']=="Blocked" ? "selected" : "" ?>>Blocked</option>
              </select>
            </div>
          </div>
        </section>

        <section class="form-card">
          <h3>Property Images</h3>
          <div class="upload-box">
            <input type="file" name="images[]" accept="image/*" multiple>
            <p>Upload images (optional)</p>
          </div>
          <?php if ($edit && !empty($propertyImages)): ?>
            <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
              <?php foreach ($propertyImages as $img): ?>
                <img src="../uploads/<?= htmlspecialchars($img); ?>" style="width:100px; height:80px; object-fit:cover; border-radius:6px; border:1px solid #ddd;">
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </section>

        <section class="form-card">
          <h3>Blueprint Editor</h3>
          <div class="blueprint-editor">
            <input type="file" id="blueprintInput" name="blueprint_image" accept="image/*" style="margin-bottom:15px;">
            
            <div id="blueprintLockableArea" class="<?= $isLocked ? 'editor-locked-ui' : '' ?>">
                <div class="bp-toolbar">
                    <!-- Polygon Tool -->
                    <button type="button" class="bp-tool-btn active" data-mode="polygon" id="modePoly" title="Polygon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3z"/></svg>
                    </button>
                    <!-- Pencil Tool -->
                    <button type="button" class="bp-tool-btn" data-mode="draw" id="modeDraw" title="Pencil">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                    </button>

                    <div class="bp-divider"></div>

                    <!-- Undo (Points/Shapes) -->
                    <button type="button" class="bp-tool-btn" id="blueprintUndo" title="Undo">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 0-9-15 9 9 0 0 0-6 2.3L3 13"/></svg>
                    </button>
                    <!-- Delete All -->
                    <button type="button" class="bp-tool-btn" id="blueprintReset" title="Clear All">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                    </button>

                    <div class="bp-divider"></div>

                    <!-- Color Picker -->
                    <select id="colorSelector" class="color-picker-box">
                        <option value="red">Red</option>
                        <option value="green">Green</option>
                        <option value="blue">Blue</option>
                    </select>
                </div>

                <div class="blueprint-canvas-wrap">
                  <canvas id="blueprintCanvas"></canvas>
                </div>
            </div>
          </div>
          <input type="hidden" name="blueprint_data" id="blueprintData">
        </section>

        <section class="form-card">
          <h3>Property Documents (PDF)</h3>
          <div class="upload-box"><input type="file" name="documents[]" accept="application/pdf" multiple></div>
          <?php if ($edit): ?>
            <div style="margin-top:10px;">
                <?php $docRes = $conn->query("SELECT * FROM property_documents WHERE property_id = $id");
                while ($doc = $docRes->fetch_assoc()): ?>
                  <div style="padding:8px; background:#f3f4f6; margin-bottom:5px; border-radius:4px; font-size:13px;">
                    📄 <?= htmlspecialchars($doc['document_title']); ?>
                  </div>
                <?php endwhile; ?>
            </div>
          <?php endif; ?>
        </section>

        <section class="form-card">
          <h3>Description</h3>
          <textarea name="description" rows="5" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;" required><?= $edit ? htmlspecialchars($property['description']) : '' ?></textarea>
        </section>

        <section class="form-card">
          <h3>Amenities</h3>
          <div class="amenities-grid">
            <?php foreach ($amenities as $a): ?>
              <label class="amenity-item">
                <input type="checkbox" name="amenities[]" value="<?= $a['id'] ?>" <?= in_array($a['id'], $selectedAmenities) ? "checked" : "" ?>> 
                <span><?= htmlspecialchars($a['name']) ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        </section>

        <div class="form-actions" style="margin-top:20px; display:flex; gap:10px;">
          <a href="list.php" class="btn-outline" style="text-decoration:none; padding:10px 20px;">Cancel</a>
          <button type="submit" class="btn-primary" style="padding:10px 30px; cursor:pointer;">Save Property</button>
        </div>
      </form>
    </div>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
(function() {
    const canvas = document.getElementById('blueprintCanvas'), ctx = canvas.getContext('2d'),
          fileInput = document.getElementById('blueprintInput'), resetBtn = document.getElementById('blueprintReset'),
          undoBtn = document.getElementById('blueprintUndo'), colorSelector = document.getElementById('colorSelector'),
          modeBtns = document.querySelectorAll('[data-mode]'), dataInput = document.getElementById('blueprintData'),
          lockableArea = document.getElementById('blueprintLockableArea'),
          form = document.querySelector('.property-form');

    if (!canvas) return;

    let img = new Image(), shapes = [], currentPoints = [], activeColor = 'red', activeMode = 'polygon',
        isDrawing = false, hasBase = false, isLocked = <?= $isLocked ? 'true' : 'false' ?>;

    let isDraggingShape = false, isDraggingDot = false, dragShape = null, dragDotIndex = -1, dragOffset = { x: 0, y: 0 };

    canvas.width = 1000; canvas.height = 600;
    
    const fillMap = { 
        'red': 'rgba(239, 68, 68, 0.3)', 
        'green': 'rgba(34, 197, 94, 0.3)', 
        'blue': 'rgba(59, 130, 246, 0.3)' 
    };

    function getMousePos(e) {
        const rect = canvas.getBoundingClientRect();
        return { 
            x: (e.clientX - rect.left) * (canvas.width / rect.width), 
            y: (e.clientY - rect.top) * (canvas.height / rect.height) 
        };
    }

    function isPointInPolygon(p, polygon) {
        let isInside = false;
        for (let i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
            if (((polygon[i].y > p.y) !== (polygon[j].y > p.y)) &&
                (p.x < (polygon[j].x - polygon[i].x) * (p.y - polygon[i].y) / (polygon[j].y - polygon[i].y) + polygon[i].x)) {
                isInside = !isInside;
            }
        }
        return isInside;
    }

    function render() {
        if (!hasBase) return;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        
        shapes.forEach(s => {
            drawShape(s, true);
            if (s.type === 'polygon') drawHandles(s.points, s.color);
        });

        if (currentPoints.length > 0) {
            drawShape({ type: activeMode, points: currentPoints, color: activeColor }, false);
            if (activeMode === 'polygon') drawHandles(currentPoints, activeColor);
        }
    }

    function drawShape(s, closed) {
        if (s.points.length < 1) return;
        ctx.beginPath(); 
        ctx.strokeStyle = s.color; 
        ctx.lineWidth = 3; 
        ctx.lineJoin = "round";
        ctx.lineCap = "round";
        ctx.moveTo(s.points[0].x, s.points[0].y);
        s.points.forEach(p => ctx.lineTo(p.x, p.y));
        
        if (closed && s.type === 'polygon' && s.points.length > 2) { 
            ctx.closePath(); 
            ctx.stroke(); 
            ctx.fillStyle = fillMap[s.color]; 
            ctx.fill(); 
        } else {
            ctx.stroke();
        }
    }

    function drawHandles(pts, color) {
        pts.forEach(p => {
            ctx.fillStyle = "#fff"; ctx.strokeStyle = color; ctx.lineWidth = 2;
            ctx.beginPath(); ctx.arc(p.x, p.y, 6, 0, Math.PI*2); ctx.fill(); ctx.stroke();
        });
    }

    canvas.addEventListener('mousedown', e => {
        if (!hasBase || isLocked) return;
        const pos = getMousePos(e);

        // 1. Check for Dot Dragging first (higher priority)
        for (let i = shapes.length - 1; i >= 0; i--) {
            if (shapes[i].type === 'polygon') {
                for (let j = 0; j < shapes[i].points.length; j++) {
                    const p = shapes[i].points[j];
                    const dist = Math.sqrt((pos.x - p.x)**2 + (pos.y - p.y)**2);
                    if (dist < 10) {
                        isDraggingDot = true;
                        dragShape = shapes[i];
                        dragDotIndex = j;
                        return;
                    }
                }
            }
        }

        // 2. Check for Shape Dragging
        for (let i = shapes.length - 1; i >= 0; i--) {
            if (shapes[i].type === 'polygon' && isPointInPolygon(pos, shapes[i].points)) {
                isDraggingShape = true;
                dragShape = shapes[i];
                dragOffset = { x: pos.x, y: pos.y };
                return;
            }
        }
        
        // 3. Polygon Logic (Start/Add point)
        if (activeMode === 'polygon') {
            if (currentPoints.length >= 3) {
                const p0 = currentPoints[0];
                const dist = Math.sqrt((pos.x - p0.x)**2 + (pos.y - p0.y)**2);
                if (dist < 15) { // Clicked start point to close
                    shapes.push({ type: 'polygon', points: [...currentPoints], color: activeColor });
                    currentPoints = [];
                    render();
                    return;
                }
            }
            currentPoints.push(pos);
        } else { 
            isDrawing = true; 
            currentPoints = [pos]; 
        }
        render();
    });

    canvas.addEventListener('mousemove', e => {
        if (isLocked || !hasBase) return;
        const pos = getMousePos(e);

        // Update dragging dot
        if (isDraggingDot && dragShape) {
            dragShape.points[dragDotIndex] = pos;
            render();
            return;
        }

        // Update dragging shape
        if (isDraggingShape && dragShape) {
            const dx = pos.x - dragOffset.x;
            const dy = pos.y - dragOffset.y;
            dragShape.points.forEach(p => { p.x += dx; p.y += dy; });
            dragOffset = { x: pos.x, y: pos.y };
            render();
            return;
        }

        if (isDrawing && activeMode === 'draw') {
            currentPoints.push(pos);
            render();
            return;
        }

        // Cursor feedback
        let hoveredDot = false;
        let hoveredShape = false;
        for (let i = shapes.length - 1; i >= 0; i--) {
            if (shapes[i].type === 'polygon') {
                shapes[i].points.forEach(p => {
                    if (Math.sqrt((pos.x - p.x)**2 + (pos.y - p.y)**2) < 10) hoveredDot = true;
                });
                if (isPointInPolygon(pos, shapes[i].points)) hoveredShape = true;
            }
        }

        if (hoveredDot) canvas.style.cursor = 'move';
        else if (hoveredShape) canvas.style.cursor = 'grab';
        else canvas.style.cursor = (activeMode === 'polygon' ? 'crosshair' : 'default');
    });

    window.addEventListener('mouseup', () => {
        isDraggingShape = false;
        isDraggingDot = false;
        dragShape = null;
        dragDotIndex = -1;

        if (isDrawing && activeMode === 'draw') { 
            if (currentPoints.length > 1) {
                shapes.push({ type: 'draw', points: [...currentPoints], color: activeColor }); 
            }
            currentPoints = []; 
        }
        isDrawing = false; 
        render();
    });

    fileInput.onchange = e => {
        const reader = new FileReader();
        reader.onload = f => { 
            img.onload = () => { 
                hasBase = true; 
                shapes = []; 
                currentPoints = []; 
                isLocked = false;
                lockableArea.classList.remove('editor-locked-ui');
                render(); 
            }; 
            img.src = f.target.result; 
        };
        if(e.target.files[0]) reader.readAsDataURL(e.target.files[0]);
    };

    modeBtns.forEach(btn => btn.onclick = () => { 
        activeMode = btn.dataset.mode; 
        modeBtns.forEach(b => b.classList.toggle('active', b === btn)); 
        currentPoints = []; 
        render(); 
    });

    colorSelector.onchange = (e) => { activeColor = e.target.value; render(); };
    
    undoBtn.onclick = () => { 
        if (currentPoints.length > 0) currentPoints.pop();
        else shapes.pop(); 
        render(); 
    };

    resetBtn.onclick = () => { 
        shapes = []; 
        currentPoints = []; 
        render(); 
    };

    form.onsubmit = () => { 
        if (hasBase && !isLocked) { 
            render(); 
            dataInput.value = canvas.toDataURL('image/png'); 
        } 
    };

    <?php if ($edit && !empty($blueprint['annotated_path'])): ?>
    img.onload = () => { 
        hasBase = true; 
        render(); 
    };
    img.src = "../uploads/blueprints/<?= $blueprint['annotated_path'] ?>";
    <?php endif; ?>
})();
</script>