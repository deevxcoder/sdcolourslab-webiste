import 'package:flutter/foundation.dart';
import 'package:lab_desktop_app/services/api_service.dart';
import 'package:lab_desktop_app/models/user_model.dart';

class PhotographerProvider with ChangeNotifier {
  List<User> _photographers = [];
  bool _isLoading = false;
  String? _errorMessage;

  List<User> get photographers => _photographers;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  final ApiService _apiService = ApiService();

  Future<void> fetchPhotographers({String? status}) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    final query = status != null ? '?status=$status' : '';
    final result = await _apiService.get('/admin/photographers$query');

    if (result['success'] == true) {
      _photographers = (result['data'] as List).map((u) => User.fromJson(u)).toList();
    } else {
      _errorMessage = result['message'];
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<bool> updateStatus(int userId, String status) async {
    final result = await _apiService.patch('/admin/photographers/$userId', {
      'status': status,
    });

    if (result['success'] == true) {
      await fetchPhotographers();
      return true;
    } else {
      _errorMessage = result['message'];
      notifyListeners();
      return false;
    }
  }
}
