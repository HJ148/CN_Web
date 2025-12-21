public function up(): void
    {
        Schema::create('sinh_viens', function (Blueprint $table) {
            $table->id(); // Tự động tạo cột 'id' (bigint, auto-increment, primary key)
            // TODO 5: Thêm 2 dòng này
            $table->string('ten_sinh_vien', 255);
            $table->string('email', 255)->unique(); // unique() là ràng buộc duy nhất
            $table->timestamps();
        });
    }
