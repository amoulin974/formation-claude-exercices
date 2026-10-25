cmake_minimum_required(VERSION 3.20)
project(tournoi LANGUAGES CXX)

set(CMAKE_CXX_STANDARD 20)
set(CMAKE_CXX_STANDARD_REQUIRED ON)
set(CMAKE_EXPORT_COMPILE_COMMANDS ON)

option(TOURNOI_SANITIZERS "Activer ASan et UBSan" OFF)
if(TOURNOI_SANITIZERS)
  add_compile_options(-fsanitize=address,undefined -fno-omit-frame-pointer)
  add_link_options(-fsanitize=address,undefined)
endif()

add_library(tournoi src/calendrier.cpp src/classement.cpp)
target_include_directories(tournoi PUBLIC include)
target_compile_options(tournoi PRIVATE -Wall -Wextra -Wpedantic)

add_executable(tournoi-cli src/main.cpp)
target_link_libraries(tournoi-cli PRIVATE tournoi)

# GoogleTest : version installée sur le système, sinon téléchargement
find_package(GTest QUIET)
if(NOT GTest_FOUND)
  include(FetchContent)
  FetchContent_Declare(googletest
    URL https://github.com/google/googletest/archive/refs/tags/v1.15.2.tar.gz)
  FetchContent_MakeAvailable(googletest)
endif()

enable_testing()
add_executable(tests tests/test_calendrier.cpp tests/test_classement.cpp)
target_link_libraries(tests PRIVATE tournoi GTest::gtest_main)
include(GoogleTest)
gtest_discover_tests(tests)
