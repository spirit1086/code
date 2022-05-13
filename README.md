# code
# в коде больше отражение стилистики написания кода, его читабельности, а не уровень сложности бизнес задачи
Три этапа вакансии:
Внутрений 3д, 
ДЗО 3д,
Внешний от 5дн и не ограничено. 
При создании и обновлении вакансии вызывается Observer, который высчитывает кол-во дней по определенной логике не включая выходные и  праздничные дни, 
при переходе вакансии по этапам отправляются уведомления определенным группам в зависимости на каком этапе вакансия
Две точки входа:
```php
app/Console/Commands/VacancyChangeSteps
/Observers/VacancyObserver
```
