import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:lab_desktop_app/services/api_service.dart';
import 'package:lab_desktop_app/models/user_model.dart';

class AuthProvider with ChangeNotifier {
  User? _user;
  String? _token;
  bool _isLoading = false;
  String? _errorMessage;

  User? get user => _user;
  String? get token => _token;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  bool get isAuthenticated => _token != null;

  final ApiService _apiService = ApiService();

  Future<void> login(String email, String password) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    final result = await _apiService.post('/auth/login', {
      'email': email,
      'password': password,
    });

    if (result['success'] == true) {
      _token = result['data']['token'];
      _user = User.fromJson(result['data']['user']);
      
      // Check if user is admin
      if (_user?.role != 'admin') {
        _token = null;
        _user = null;
        _errorMessage = 'Access denied. Only administrators can use this app.';
      } else {
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', _token!);
      }
    } else {
      _errorMessage = result['message'];
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<void> logout() async {
    await _apiService.post('/auth/logout', {});
    _token = null;
    _user = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
    notifyListeners();
  }

  Future<void> checkSession() async {
    final prefs = await SharedPreferences.getInstance();
    final savedToken = prefs.getString('auth_token');
    
    if (savedToken != null) {
      _token = savedToken;
      final result = await _apiService.get('/auth/me');
      if (result['success'] == true) {
        _user = User.fromJson(result['data']);
        if (_user?.role != 'admin') {
          await logout();
        }
      } else {
        _token = null;
        await prefs.remove('auth_token');
      }
    }
    notifyListeners();
  }
}
