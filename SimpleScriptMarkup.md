# Introduction #

Writing a screenplay-formatted blog post should be extremely simple.  Simple Script Markup (SSM) uses very simple rules to decide on various screenplay elements.


# Details #

Enclosing text inside of an 

&lt;ssm&gt;

 tag tells the plugin that the enclosed text is meant to be parsed by the plugin.

The rules are quite simple:

  * Scene Heading - All Caps, formatted like so: INT. LOCATION or EXT. LOCATION
  * Character Name - ALL CAPS, one space preceding the name, ending in a colon.
  * Parenthetical - One space preceding the name, surrounded by parentheses.
  * Dialogue - One space preceding the dialogue.
  * Scene transition - ALL CAPS, ending with a colon.
  * Action - Normal text

# Example #

Here is an example of text that will be parsed by SSM.

```
<ssm>
EXT. PLAYGROUND

This is action text.  This is meant to describe what is happening in the scene.  In this scene, CHARACTER 1 and CHARACTER 2 are having a conversation about SSM.

 CHARACTER 1:
 Did you notice the space before my name, as well as the space before this dialogue?

 CHARACTER 2:
 (annoyed)
 Of course I noticed.  That's how SSM works.

CUT TO:

INT. OFFICE

Some other stuff happens, and it's funny because office dynamics are hysterical.

END
</ssm>
```