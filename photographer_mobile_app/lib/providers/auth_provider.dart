import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:photographer_mobile_app/services/api_service.dart';
import 'package:photographer_mobile_app/models/user_model.dart';

enum AuthStatus { unauthenticated, authenticated, pending, rejected }

class AuthProvider with ChangeNotifier {
  User? _user;
  AuthStatus _status = AuthStatus.unauthenticated;
  bool _isLoading = false;
  String? _errorMessage;

  User? get user => _user;
  AuthStatus get status => _status;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  final ApiService _apiService = ApiService();

  Future<void> checkSession() async {
    _isLoading = true;
    notifyListeners();

    final result = await _apiService.get('/auth/me');
    if (result['success'] == true) {
      _user = User.fromJson(result['data']);
      _updateStatusFromUser();
    } else {
      _status = AuthStatus.unauthenticated;
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<bool> login(String email, String password) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    final result = await _apiService.post('/auth/login', {
      'email': email,
      'password': password,
    });

    if (result['success'] == true) {
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('auth_token', result['data']['token']);
      
      _user = User.fromJson(result['data']['user']);
      _updateStatusFromUser();
      
      _isLoading = false;
      notifyListeners();
      return true;
    } else {
      _errorMessage = result['message'];
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> register({
    required String name,
    required String email,
    required String password,
    required String phone,
    String? studioName,
    String? city,
  }) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    final result = await _apiService.post('/auth/register', {
      'name': name,
      'email': email,
      'password': password,
      'phone': phone,
      'studio_name': studioName,
      'city': city,
    });

    if (result['success'] == true) {
      _isLoading = false;
      notifyListeners();
      return true;
    } else {
      _errorMessage = result['message'];
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  void _updateStatusFromUser() {
    if (_user == null) {
      _status = AuthStatus.unauthenticated;
    } else if (_user!.status == 'approved') {
      _status = AuthStatus.authenticated;
    } else if (_user!.status == 'rejected') {
      _status = AuthStatus.rejected;
    } else {
      _status = AuthStatus.pending;
    }
  }

  Future<void> logout() async {
    await _apiService.post('/auth/logout', {});
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
    _user = null;
    _status = AuthStatus.unauthenticated;
    notifyListeners();
  }
}
