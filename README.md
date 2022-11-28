# Progress status evaluation and estimation API
The goal is to create an API that determines if it’s achievable to go through the duration of
learning content (in reality video time) with a current learning progress until a due date.
Depending on the difference between the actual and ideal progress at the time of the call the
API returns if the user is on track or not.

#### Endpoint design

- The endpoint should be RESTful and naming recommendations compliant
- The endpoint should be designed following the best practices
- Request method - GET

#### Request parameters
- Course duration in seconds → integer
- Current learning progress in percentage → integer
- Assignment creation date → datetime (RFC3339)
- Due date → datetime (RFC3339)

#### Implementation requirements
- Implementation should use business logic class
- Validate all input parameter values for cast type and plausibility.
- If the input data is invalid, the API must return error code and message as you see
appropriate
- If the input data is valid, the API must process the input and respond with json encoded
data in the body. Response data properties should be:
    - progress_status: string → “on track” | “not on track” | “overdue”
    - expected_progress: integer → the expected progress value at the moment
    - needed_daily_learning_time: integer → learning time per day in seconds

- Define and handle edge cases

#### Criteria for “progress_status” values “on track”, “not on track” or “overdue”
- “on track” is when the current learning progress is equal or greater than the ideal
progress expected at the time, when the API was requested
- “not on track” is when the current learning progress is less than the ideal progress
expected at the time, when the API was requested
- “overdue” is when the due date is in the past already and the progress is less than 100%

#### Definition of result field “expected_progress”
The field “expected_progress” contains the ideal progress percentage that is expected to have
been achieved at the time of the request.
#### Definition of result field “needed_daily_learning_time”
Daily learning time needed to achieve the goal.

## Installation
wip
