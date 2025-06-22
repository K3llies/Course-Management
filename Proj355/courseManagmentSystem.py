import mysql.connector

def connect_to_database():
    return mysql.connector.connect(
        host="localhost",
        user="brandonm",
        password="csit355brandon",
        database="csit355"
    )

# Function to show available commands
def show_help_prompt():
    print("Help:")
    print("L: List all available courses",
          "\nE: Enroll yourself in a course",
          "\nW: Withdraw yourself from a course",
          "\nS: Search for a course by name",
          "\nM: List your current enrollments",
          "\nP: Prerequisites for a course",
          "\nT: List all professor information",
          "\nH: List executable functions",
          "\nX: Exit application")

# Check if student ID exists
def check_if_sid_exists(conn, sid):
    with conn.cursor() as cur:
        cur.execute("SELECT COUNT(*) FROM Students WHERE sid = %s", (sid,))
        result = cur.fetchone()
        return result[0] > 0

# Check if course ID exists
def check_if_cid_exists(conn, cid):
    with conn.cursor() as cur:
        cur.execute("SELECT COUNT(*) FROM Courses WHERE cid = %s", (cid,))
        result = cur.fetchone()
        return result[0] > 0

# Check if a student is enrolled in a specific course
def check_if_enrollment_exists(conn, sid, cid):
    with conn.cursor() as cur:
        cur.execute("SELECT COUNT(*) FROM Enrolled WHERE sid = %s AND cid = %s", (sid, cid))
        result = cur.fetchone()
        return result[0] > 0

# Fetch student name
def get_student_name(conn, sid):
    with conn.cursor() as cur:
        cur.execute("SELECT sname FROM Students WHERE sid = %s", (sid,))
        result = cur.fetchone()
        return result[0] if result else None

# Create a new student
def create_student(conn, sid, sname, age):
    with conn.cursor() as cur:
        cur.execute("INSERT INTO Students (sid, sname, age) VALUES (%s, %s, %s)", (sid, sname, age))
        conn.commit()

# Add a new student
def create_new_student(conn):
    try:
        sid = int(input("Enter Student ID: "))
        sname = input("Enter Student Name: ")
        age = int(input("Enter Student Age: "))
        create_student(conn, sid, sname, age)
        print(f"Student {sname} added successfully!")
    except ValueError:
        print("Invalid input. Please enter correct data types.")
    except mysql.connector.IntegrityError:
        print("A student with this ID already exists. Please try again.")

# List all available courses
def select_all_courses(conn):
    with conn.cursor() as cur:
        cur.execute("""
            SELECT c.cid, c.cname, c.credits, s.class_time, s.building
            FROM Courses c
            LEFT JOIN Schedules s ON c.cid = s.cid
        """)
        courses = cur.fetchall()
        if courses:
            print("CID | Course Name            | Credits | Class Time | Building")
            print("----------------------------------------------------------")
            for course in courses:
                cid, cname, credits, class_time, building = course
                print(f"{cid:<3} | {cname:<20} | {credits:<8} | {class_time:<10} | {building}")
        else:
            print("No records found in the Courses table.")

# Enroll in a course
def enroll_in_course(conn, sid):
    try:
        cid = int(input("Enter Course ID: "))
        if not check_if_cid_exists(conn, cid):
            print("Course ID does not exist.")
            return

        if check_if_enrollment_exists(conn, sid, cid):
            print("You are already enrolled in this course.")
            return

        with conn.cursor() as cur:
            cur.execute("INSERT INTO Enrolled (sid, cid) VALUES (%s, %s)", (sid, cid))
            conn.commit()
        print("You have successfully enrolled in the course.")
    except ValueError:
        print("Invalid input. Please enter a valid course ID.")
    except mysql.connector.Error as e:
        print(f"Error: {e}")

# Withdraw from a course
def withdraw_from_course(conn, sid):
    try:
        cid = int(input("Enter Course ID: "))
        if not check_if_enrollment_exists(conn, sid, cid):
            print("You are not enrolled in this course.")
            return

        with conn.cursor() as cur:
            cur.execute("DELETE FROM Enrolled WHERE sid = %s AND cid = %s", (sid, cid))
            conn.commit()
        print("You have withdrawn from the course.")
    except ValueError:
        print("Invalid input. Please enter a valid course ID.")
    except mysql.connector.Error as e:
        print(f"Error: {e}")

# Search for a course
def search_for_course(conn):
    substring = input("Enter a substring to search for courses: ")
    with conn.cursor() as cur:
        cur.execute("""
            SELECT c.cid, c.cname, c.credits, s.class_time, s.building
            FROM Courses c
            LEFT JOIN Schedules s ON c.cid = s.cid
            WHERE c.cname LIKE %s
        """, (f"%{substring}%",))
        courses = cur.fetchall()
        if courses:
            print("CID | Course Name            | Credits | Class Time | Building")
            print("----------------------------------------------------------")
            for course in courses:
                cid, cname, credits, class_time, building = course
                print(f"{cid:<3} | {cname:<20} | {credits:<8} | {class_time:<10} | {building}")
        else:
            print("No matching courses found.")

# View enrolled courses
def view_my_classes(conn, sid):
    with conn.cursor() as cur:
        cur.execute("""
            SELECT c.cid, c.cname, c.credits, s.class_time, s.building
            FROM Enrolled e
            JOIN Courses c ON e.cid = c.cid
            LEFT JOIN Schedules s ON c.cid = s.cid
            WHERE e.sid = %s
        """, (sid,))
        courses = cur.fetchall()
        if courses:
            print("CID | Course Name            | Credits | Class Time | Building")
            print("----------------------------------------------------------")
            for course in courses:
                cid, cname, credits, class_time, building = course
                print(f"{cid:<3} | {cname:<20} | {credits:<8} | {class_time:<10} | {building}")
        else:
            print("You are not enrolled in any courses.")

# View prerequisites for a course
def view_course_prereqs(conn, cid):
    with conn.cursor() as cur:
        cur.execute("SELECT prereq_cid FROM Prerequisites WHERE cid = %s", (cid,))
        prerequisites = cur.fetchall()
        if prerequisites:
            print(f"Prerequisites for Course {cid}:")
            for prereq in prerequisites:
                print(f"The prerequisite for {cid} is {prereq[0]}")
        else:
            print(f"No prerequisites found for Course {cid}.")

# View professor information
def view_teaching_prof(conn):
    with conn.cursor() as cur:
        cur.execute("""
            SELECT p.pid, p.pname, p.department, t.cid
            FROM Professor p
            LEFT JOIN Teaching t ON p.pid = t.pid
        """)
        professors = cur.fetchall()
        if professors:
            print("PID | Professor Name            | Department       | Course")
            print("---------------------------------------------------------------")
            for prof in professors:
                pid, pname, department, cid = prof
                print(f"{pid:<3} | {pname:<20} | {department:<15} | {cid if cid else 'None'}")
        else:
            print("No professor records found.")

# Main command interface
def start_cmd_interface(conn):
    print("Welcome to MSU Course Registration System!")

    while True:
        try:
            sid = int(input("Enter your student ID (or enter -1 to sign up): "))
            if sid == -1:
                create_new_student(conn)
            elif check_if_sid_exists(conn, sid):
                break
            else:
                print("Student ID does not exist in the database.")
        except ValueError:
            print("Student ID must be an integer.")

    print("Welcome back,", get_student_name(conn, sid), "!")

    while True:
        user_input = input("Enter a command (H for help): ").strip().upper()
        if user_input == 'L':
            select_all_courses(conn)
        elif user_input == 'E':
            enroll_in_course(conn, sid)
        elif user_input == 'W':
            withdraw_from_course(conn, sid)
        elif user_input == 'S':
            search_for_course(conn)
        elif user_input == 'M':
            view_my_classes(conn, sid)
        elif user_input == 'P':
            try:
                cid = int(input("Enter the course ID to view prerequisites: "))
                view_course_prereqs(conn, cid)
            except ValueError:
                print("Invalid course ID. Please enter an integer.")
        elif user_input == 'T':
            view_teaching_prof(conn)
        elif user_input == 'H':
            show_help_prompt()
        elif user_input == 'X':
            print("Thank you for using MSU Course Registration System!")
            break
        else:
            print("Invalid command. Enter 'H' for help.")

# Main entry point
def main():
    connection = connect_to_database()
    try:
        start_cmd_interface(connection)
    finally:
        connection.close()

if __name__ == '__main__':
    main()