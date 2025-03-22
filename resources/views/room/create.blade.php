<div class="container">
    <h2>Upload Room Images</h2>
    <form action="{{ route('rooms.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="capacity" class="form-label">Capacity</label>
            <input type="number" name="capacity" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="number" name="price" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label for="floor_id" class="form-label">Floor ID</label>
            <input type="number" value="10" name="floor_id" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="images" class="form-label">Upload Images</label>
            <input type="file" name="images[]" class="form-control" multiple required>
        </div>
        <button type="submit" class="btn btn-primary">Upload Room</button>
    </form>
</div>
