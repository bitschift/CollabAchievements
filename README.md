# CollabAchievements
Achievements Website for the Collaboratory

2015-12-21:
As of now the basic functionality of login, registration, requesting, endorsing, and approving achievements works. Deleting has not yet been implemented
and the code isn't fully documented, but that will get done tomorrow hopefully. As far as the new database layout goes, nothing has changed except for how
achievements are stored in the user and requests tables. Instead of storing the achievement and the level separately, the level id is instead stored, and
when achievement name is needed, cross-referencing is done.
