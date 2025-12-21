//  CODE 1
<form action="{{ route('sinhvien.store') }}" method="POST">
    @csrf
        Tên sinh viên: <input type="text" name="ten_sinh_vien" required>
        Email: <input type="email" name="email" required>
        <button type="submit">Thêm</button>
    </form>

//  CODE 2
@foreach ($danhSachSV as $sv) 
            <tr>
                <td>{{ $sv->id }}</td>
                <td>{{ $sv->ten_sinh_vien }}</td>
                <td>{{ $sv->email }}</td>
                <td>{{ $sv->created_at }}</td>
            </tr>
        @endforeach
