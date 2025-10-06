<h2>Sửa rạp chiếu</h2>
<form method="post" action="?act=rap_update&id=<?= $rap['id'] ?>">
    <label>Tên rạp:</label>
    <input type="text" name="ten" value="<?= htmlspecialchars($rap['ten']) ?>" required><br>

    <label>Địa chỉ:</label>
    <input type="text" name="dia_chi" value="<?= htmlspecialchars($rap['dia_chi']) ?>" required><br>

    <label>SĐT:</label>
    <input type="text" name="sdt" value="<?= htmlspecialchars($rap['sdt']) ?>" required><br>

    <button type="submit">Cập nhật</button>
</form>
