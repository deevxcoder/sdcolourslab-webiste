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
      id: json['id'] is String ? int.parse(json['id']) : json['id'],
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      role: json['role'] ?? 'photographer',
      phone: json['phone'],
      studioName: json['studio_name'],
      city: json['city'],
      status: json['status'] ?? 'pending',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'role': role,
      'phone': phone,
      'studio_name': studioName,
      'city': city,
      'status': status,
    };
  }
}
