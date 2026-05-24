class User {
  final int id;
  final String name;
  final String email;
  final String role;
  final String? phone;
  final String? studioName;
  final String? city;
  final String status;

  User({
    required this.id,
    required this.name,
    required this.email,
    required this.role,
    this.phone,
    this.studioName,
    this.city,
    required this.status,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      name: json['name'],
      email: json['email'],
      role: json['role'],
      phone: json['phone']?.toString(),
      studioName: json['studio_name']?.toString(),
      city: json['city']?.toString(),
      status: json['status'] ?? 'pending',
    );
  }
}
